<?php

namespace App\Observers;


use App\Models\Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ModelObserver
{
    /**
     * Centralized method to log events.
     */
    public function logEvent($model, string $event, string $action): void
    {
        try {
            // Gather meta data
            $metaData = [
                'attributes' => $model->getAttributes(),
                'changes' => $model->getChanges(),
                'original' => $model->getOriginal(),
            ];

            // Capture request context if available
            $context = null;
            if (app()->runningInConsole() === false) {
                $context = [
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::header('User-Agent'),
                ];
            }

            Record::create([
                'event_type'  => get_class($model) . '_' . $event,
                'entity_id'   => $model->id,
                'entity_type' => get_class($model),
                'action'      => $action,
                'description' => 'Record ' . $action . 'd in ' . get_class($model),
                'meta_data'   => array_merge($metaData, $context ?? []),
                'occurred_at' => now(),
                'status'      => 'unread',
            ]);
        } catch (\Exception $e) {
            // Log the exception without interrupting the main process
            \Log::error('Failed to log model event: ' . $e->getMessage());
        }
    }

    public function created($model)
    {
        $this->logEvent($model, 'created', 'create');
    }
    public function updated($model): void
    {
        $this->logEvent($model, 'updated', 'update');
    }

    public function deleted($model): void
    {
//        \Log::channel('deletions')->info('Model deleted');
        $this->logEvent($model, 'deleted', 'delete');
    }

    // Optionally handle restored events if using soft deletes
    public function restored($model): void
    {
        $this->logEvent($model, 'restored', 'restore');
    }

    public function forceDeleted($model): void
    {
        $this->logEvent($model, 'forceDeleted', 'delete');
    }

    public function softDeleted($model): void
    {
        $this->logEvent($model, 'softDeleted', 'delete');
    }
}
