<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LogService
{
    /**
     * Log a custom activity for any model.
     */
    public function log(
        Model $model,
        string $action,
        ?string $description = null,
        ?string $columnName = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'column_name' => $columnName,
            'old_value' => $this->serializeValue($oldValue),
            'new_value' => $this->serializeValue($newValue),
            'description' => $description,
        ]);
    }

    /**
     * Log a custom activity without a specific model.
     */
    public function logGeneric(
        string $action,
        string $description,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'model_type' => 'System',
            'model_id' => 0,
            'action' => $action,
            'description' => $description,
        ]);
    }

    /**
     * Get logs for a specific model.
     */
    public function getLogsFor(Model $model)
    {
        return ActivityLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get logs for a specific user.
     */
    public function getLogsByUser(int $userId)
    {
        return ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get logs by action type.
     */
    public function getLogsByAction(string $action)
    {
        return ActivityLog::where('action', $action)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
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
}
