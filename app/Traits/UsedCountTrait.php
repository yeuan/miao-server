<?php

namespace App\Traits;

trait UsedCountTrait
{
    public function created($model): void
    {
        $targetId = $model->{$this->usedCountField} ?? null;
        if ($targetId) {
            ($this->usedCountModel)::where('id', $targetId)->increment('used_count');
        }
    }

    public function deleted($model): void
    {
        $targetId = $model->{$this->usedCountField} ?? null;
        if ($targetId) {
            ($this->usedCountModel)::where('id', $targetId)
                ->where('used_count', '>', 0)
                ->decrement('used_count');
        }
    }

    public function updated($model): void
    {
        if ($model->isDirty($this->usedCountField)) {
            $originalTargetId = $model->getOriginal($this->usedCountField);
            $newTargetId = $model->{$this->usedCountField};

            if ($originalTargetId) {
                ($this->usedCountModel)::where('id', $originalTargetId)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }
            if ($newTargetId) {
                ($this->usedCountModel)::where('id', $newTargetId)->increment('used_count');
            }
        }
    }
}
