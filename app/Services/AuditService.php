<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    /**
     * Log a system action silently. Never throws an exception outward.
     *
     * @param string $action
     * @param object $model     The Eloquent model affected
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return void
     */
    public function log(string $action, $model, array $oldValues = null, array $newValues = null): void
    {
        try {
            $userId    = null;
            $ipAddress = null;
            $userAgent = null;

            if (auth()->check()) {
                $userId = auth()->id();
            }

            if (app()->runningInConsole() === false && request()) {
                $ipAddress = request()->ip();
                $userAgent = request()->userAgent();
            }

            AuditLog::create([
                'user_id'        => $userId,
                'action'         => $action,
                'auditable_type' => get_class($model),
                'auditable_id'   => $model->id,
                'old_values'     => $oldValues,
                'new_values'     => $newValues,
                'ip_address'     => $ipAddress,
                'user_agent'     => $userAgent,
            ]);
        } catch (\Throwable $e) {
            // Silently fail — audit must never break the main business action
        }
    }
}
