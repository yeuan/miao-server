<?php

namespace App\Models\Content;

use App\Models\BaseModel;

class Notice extends BaseModel
{
    protected $table = 'notice';

    protected $guarded = ['id'];

    public function getStartTimeAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }

    public function getEndTimeAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }
}
