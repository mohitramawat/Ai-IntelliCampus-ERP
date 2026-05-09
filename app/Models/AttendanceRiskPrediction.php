<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRiskPrediction extends Model
{
    protected $fillable = [
        'student_id',
        'attendance_percentage',
        'total_present',
        'total_absent',
        'total_lectures',
        'consecutive_absences',
        'risk_level',
        'ai_remark',
        'suggested_action',
        'ai_model_used',
        'raw_ai_response',
        'prediction_date',
    ];

    protected $casts = [
        'attendance_percentage' => 'float',
        'total_present'         => 'integer',
        'total_absent'          => 'integer',
        'total_lectures'        => 'integer',
        'consecutive_absences'  => 'integer',
        'raw_ai_response'       => 'array',
        'prediction_date'       => 'date',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ─── Accessors / Helpers ────────────────────────────────────────────────────

    /**
     * Get a Tailwind color class based on risk level.
     */
    public function getRiskColorClassAttribute(): string
    {
        return match ($this->risk_level) {
            'high'   => 'text-red-400',
            'medium' => 'text-yellow-400',
            default  => 'text-emerald-400',
        };
    }

    /**
     * Get Tailwind badge classes for the risk level.
     */
    public function getRiskBadgeClassAttribute(): string
    {
        return match ($this->risk_level) {
            'high'   => 'bg-red-500/20 text-red-400 border border-red-500/30',
            'medium' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
            default  => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
        };
    }

    /**
     * Get human-readable risk label with emoji.
     */
    public function getRiskLabelAttribute(): string
    {
        return match ($this->risk_level) {
            'high'   => 'High Risk 🚨',
            'medium' => 'Medium Risk ⚠️',
            default  => 'Low Risk ✅',
        };
    }
}
