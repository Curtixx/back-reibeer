<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('created', 'Registro criado');
        });

        static::updated(function ($model) {
            $model->logChanges();
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Registro excluído');
        });
    }

    /**
     * Log a single activity.
     */
    public function logActivity(string $action, ?string $description = null, ?string $columnName = null, mixed $oldValue = null, mixed $newValue = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'action' => $action,
            'column_name' => $columnName,
            'old_value' => $this->serializeValue($oldValue),
            'new_value' => $this->serializeValue($newValue),
            'description' => $description,
        ]);
    }

    /**
     * Log all changes made to the model.
     */
    protected function logChanges(): void
    {
        $changes = $this->getChanges();
        $original = $this->getOriginal();

        // Remove timestamps automáticos se não quiser logar
        unset($changes['updated_at'], $changes['created_at']);

        foreach ($changes as $column => $newValue) {
            $oldValue = $original[$column] ?? null;

            // Só loga se realmente mudou
            if ($oldValue !== $newValue) {
                $this->logActivity(
                    action: 'updated',
                    description: "Campo '{$column}' atualizado",
                    columnName: $column,
                    oldValue: $oldValue,
                    newValue: $newValue
                );
            }
        }
    }

    /**
     * Serialize value for storage.
     */
    protected function serializeValue(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Get all activity logs for this model.
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }
}
