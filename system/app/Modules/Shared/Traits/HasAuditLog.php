<?php

namespace App\Modules\Shared\Traits;

trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        $auditEvents = ['created', 'updated', 'deleted'];

        foreach ($auditEvents as $event) {
            static::$event(function ($model) use ($event) {
                // Only log if AuditLogService exists
                if (app()->bound(\App\Modules\AuditLog\Services\AuditLogService::class)) {
                    app(\App\Modules\AuditLog\Services\AuditLogService::class)->log($model, $event);
                }
            });
        }
    }
}
