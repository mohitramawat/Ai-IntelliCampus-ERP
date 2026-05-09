<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Required documents every student must submit.
     */
    protected array $requiredDocs = [
        '10th_marksheet' => '10th Marksheet',
        '12th_marksheet' => '12th Marksheet',
        'aadhaar'        => 'Aadhaar Card',
    ];

    public function __construct(
        protected StudentDocumentService $documentService
    ) {}

    /**
     * Show student's document centre — uploaded + pending docs.
     */
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (! $student) {
            abort(404, 'Student profile not found.');
        }

        // Load existing uploads keyed by document_type
        $uploaded = $student->documents()
            ->whereIn('document_type', array_keys($this->requiredDocs))
            ->get()
            ->keyBy('document_type');

        $totalRequired  = count($this->requiredDocs);
        $totalUploaded  = $uploaded->count();
        $totalPending   = $totalRequired - $totalUploaded;
        $progressPct    = $totalRequired > 0
            ? round(($totalUploaded / $totalRequired) * 100)
            : 0;

        return view('student.documents.index', compact(
            'student',
            'uploaded',
            'totalRequired',
            'totalUploaded',
            'totalPending',
            'progressPct',
        ))->with('requiredDocs', $this->requiredDocs);
    }

    /**
     * Handle a student uploading a single document.
     */
    public function upload(Request $request)
    {
        $user    = auth()->user();
        $student = $user->student;

        if (! $student) {
            return back()->withErrors(['error' => 'Student profile not found.']);
        }

        $validDocTypes = implode(',', array_keys($this->requiredDocs));

        $request->validate([
            'document_type' => ['required', 'string', "in:{$validDocTypes}"],
            'document'      => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'document.required'  => 'Please select a file to upload.',
            'document.mimes'     => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
            'document.max'       => 'File size must not exceed 5 MB.',
            'document_type.in'   => 'Invalid document type selected.',
        ]);

        try {
            $this->documentService->uploadDocument(
                $student,
                $request->file('document'),
                $request->document_type
            );

            $docLabel = $this->requiredDocs[$request->document_type] ?? $request->document_type;

            return back()->with('success', "✅ {$docLabel} uploaded successfully!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete / retract a previously uploaded document.
     */
    public function delete(Request $request)
    {
        $user    = auth()->user();
        $student = $user->student;

        if (! $student) {
            return back()->withErrors(['error' => 'Student profile not found.']);
        }

        $request->validate([
            'document_type' => ['required', 'string'],
        ]);

        $document = $student->documents()
            ->where('document_type', $request->document_type)
            ->first();

        if (! $document) {
            return back()->withErrors(['error' => 'Document not found.']);
        }

        try {
            $this->documentService->deleteDocument($document);
            $docLabel = $this->requiredDocs[$request->document_type] ?? $request->document_type;
            return back()->with('success', "🗑️ {$docLabel} removed. You can re-upload anytime.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
    }
}
