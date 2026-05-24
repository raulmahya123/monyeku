<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit($model, 'created', null, $model->toArray());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $old = [];
            $new = [];
            foreach ($changes as $key => $value) {
                if (in_array($key, ['updated_at'])) continue;
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $value;
            }
            if (!empty($new)) {
                static::logAudit($model, 'updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            static::logAudit($model, 'deleted', $model->toArray(), null);
        });
    }

    protected static function logAudit($model, string $event, $oldValues, $newValues): void
    {
        $user = Auth::user();
        if (!$user) return;

        $companyId = $user->current_company_id ?? $model->company_id ?? null;
        if (!$companyId) return;

        AuditTrail::create([
            'company_id' => $companyId,
            'user_id' => $user->id,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logCustom(string $event, $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        static::logAudit($model, $event, $oldValues, $newValues);
    }
}
