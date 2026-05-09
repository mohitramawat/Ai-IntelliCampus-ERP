<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRiskPrediction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HuggingFaceRiskService
 *
 * Integrates with the Hugging Face Inference API to predict attendance risk
 * for a given student. The service:
 *   1. Computes attendance metrics from the database.
 *   2. Builds a structured prompt for the LLM.
 *   3. Calls the Hugging Face API.
 *   4. Parses and persists the prediction result.
 *
 * AI Model: meta-llama/Llama-3.2-3B-Instruct (conversational)
 * Fallback: rule-based classification if API is unavailable.
 */
class HuggingFaceRiskService
{
    private string $apiKey;
    private string $model;
    private string $apiBase = 'https://api-inference.huggingface.co/models/';

    public function __construct()
    {
        $this->apiKey = config('services.huggingface.api_key', env('HUGGINGFACE_API_KEY', ''));
        $this->model  = config('services.huggingface.model', 'meta-llama/Llama-3.2-3B-Instruct');
    }

    // ─── Public Entry Point ──────────────────────────────────────────────────────

    /**
     * Analyze a student's attendance and generate an AI risk prediction.
     * Persists the result and returns the model instance.
     */
    public function predictRisk(Student $student): AttendanceRiskPrediction
    {
        $metrics = $this->computeMetrics($student);
        $rawResponse = null;
        $parsed = null;

        // Attempt Hugging Face API call
        if (!empty($this->apiKey)) {
            try {
                $rawResponse = $this->callHuggingFaceApi($metrics);
                $parsed      = $this->parseApiResponse($rawResponse, $metrics);
            } catch (\Throwable $e) {
                Log::warning("[HuggingFaceRiskService] API call failed for student #{$student->id}: " . $e->getMessage());
                $parsed = null;
            }
        }

        // Fallback to rule-based if API failed or key missing
        if ($parsed === null) {
            $parsed = $this->ruleBased($metrics);
        }

        // Upsert — keep latest prediction per student
        $prediction = AttendanceRiskPrediction::updateOrCreate(
            ['student_id' => $student->id],
            [
                'attendance_percentage' => $metrics['percentage'],
                'total_present'         => $metrics['total_present'],
                'total_absent'          => $metrics['total_absent'],
                'total_lectures'        => $metrics['total_lectures'],
                'consecutive_absences'  => $metrics['consecutive_absences'],
                'risk_level'            => $parsed['risk_level'],
                'ai_remark'             => $parsed['ai_remark'],
                'suggested_action'      => $parsed['suggested_action'],
                'ai_model_used'         => $this->model,
                'raw_ai_response'       => $rawResponse,
                'prediction_date'       => now()->toDateString(),
            ]
        );

        return $prediction;
    }

    // ─── Metric Computation ──────────────────────────────────────────────────────

    /**
     * Build attendance metrics array from the student's records.
     */
    private function computeMetrics(Student $student): array
    {
        $records = AttendanceRecord::where('student_id', $student->id)
            ->join('lecture_sessions', 'attendance_records.lecture_session_id', '=', 'lecture_sessions.id')
            ->orderBy('lecture_sessions.lecture_date', 'desc')
            ->select('attendance_records.status', 'lecture_sessions.lecture_date as date')
            ->get();

        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent  = $records->where('status', 'absent')->count();
        $pct     = $total > 0 ? round(($present / $total) * 100, 2) : 0.0;

        // Consecutive absences from the most recent records
        $consecutive = 0;
        foreach ($records as $r) {
            if ($r->status === 'absent') {
                $consecutive++;
            } else {
                break;
            }
        }

        return [
            'total_lectures'       => $total,
            'total_present'        => $present,
            'total_absent'         => $absent,
            'percentage'           => $pct,
            'consecutive_absences' => $consecutive,
        ];
    }

    // ─── Hugging Face API ────────────────────────────────────────────────────────

    /**
     * Call Hugging Face Inference API and return raw JSON decoded response.
     */
    private function callHuggingFaceApi(array $metrics): ?array
    {
        $prompt = $this->buildPrompt($metrics);

        $url = $this->apiBase . $this->model . '/v1/chat/completions';

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($url, [
                'model'       => $this->model,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'You are an academic risk analysis AI. You must respond ONLY with valid JSON, no markdown, no extra text.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens'  => 300,
                'temperature' => 0.3,
            ]);

        if ($response->failed()) {
            Log::error('[HuggingFaceRiskService] API HTTP error: ' . $response->status() . ' — ' . $response->body());
            return null;
        }

        return $response->json();
    }

    /**
     * Build a structured prompt describing the student's attendance situation.
     */
    private function buildPrompt(array $metrics): string
    {
        $pct  = $metrics['percentage'];
        $cons = $metrics['consecutive_absences'];
        $tot  = $metrics['total_lectures'];
        $pre  = $metrics['total_present'];
        $abs  = $metrics['total_absent'];

        return <<<PROMPT
Analyze this student's attendance data and classify academic attendance risk.

Attendance Data:
- Total Lectures: {$tot}
- Present: {$pre}
- Absent: {$abs}
- Attendance Percentage: {$pct}%
- Consecutive Recent Absences: {$cons}

Classification Rules:
- "high" risk: attendance < 60% OR consecutive_absences >= 5
- "medium" risk: attendance 60–80% OR consecutive_absences 3–4
- "low" risk: attendance > 80% AND consecutive_absences < 3

Return ONLY valid JSON in this exact format:
{
  "risk_level": "low" | "medium" | "high",
  "ai_remark": "A concise 1–2 sentence assessment of the student's attendance situation.",
  "suggested_action": "A specific, actionable recommendation for the teacher or HOD."
}
PROMPT;
    }

    /**
     * Parse the Hugging Face API response and extract structured prediction.
     */
    private function parseApiResponse(?array $response, array $metrics): ?array
    {
        if (empty($response)) {
            return null;
        }

        // Extract text content from chat completions format
        $text = $response['choices'][0]['message']['content'] ?? null;

        if (empty($text)) {
            return null;
        }

        // Strip any markdown code fences if present
        $text = preg_replace('/```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/```/', '', $text);
        $text = trim($text);

        // Extract JSON object from the text
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $data = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $riskLevel = strtolower(trim($data['risk_level'] ?? ''));
                if (!in_array($riskLevel, ['low', 'medium', 'high'])) {
                    $riskLevel = $this->ruleBased($metrics)['risk_level'];
                }
                return [
                    'risk_level'       => $riskLevel,
                    'ai_remark'        => $data['ai_remark']        ?? 'No remark generated.',
                    'suggested_action' => $data['suggested_action']  ?? 'No action suggested.',
                ];
            }
        }

        return null;
    }

    // ─── Rule-Based Fallback ─────────────────────────────────────────────────────

    /**
     * Simple rule-based classifier used when API is unavailable.
     */
    private function ruleBased(array $metrics): array
    {
        $pct  = $metrics['percentage'];
        $cons = $metrics['consecutive_absences'];

        if ($pct < 60 || $cons >= 5) {
            return [
                'risk_level'       => 'high',
                'ai_remark'        => "Attendance is critically low at {$pct}%. The student has missed {$metrics['total_absent']} out of {$metrics['total_lectures']} lectures.",
                'suggested_action' => 'Immediate academic monitoring required. Contact student and guardian. Consider issuing a formal attendance warning letter.',
            ];
        } elseif ($pct < 80 || $cons >= 3) {
            return [
                'risk_level'       => 'medium',
                'ai_remark'        => "Attendance stands at {$pct}%, which is below the recommended 80% threshold. Proactive engagement is advised.",
                'suggested_action' => 'Schedule a counselling session with the student. Monitor attendance weekly and report to HOD if no improvement within 2 weeks.',
            ];
        } else {
            return [
                'risk_level'       => 'low',
                'ai_remark'        => "Attendance is satisfactory at {$pct}%. The student is maintaining regular class participation.",
                'suggested_action' => 'Continue monitoring. Encourage the student to maintain or improve their attendance further.',
            ];
        }
    }
}
