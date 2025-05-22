<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use ModelTrait;

    protected $dateFormat = 'U';

    protected $guarded = [];

    protected static array $timestampFields = ['start_time', 'end_time'];

    public $createdBy = true;

    public $updatedBy = true;

    public function setAttribute($key, $value)
    {
        if (in_array($key, static::$timestampFields)) {
            $value = is_numeric($value) ? $value : strtotime($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getStartTimeAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }

    public function getEndTimeAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }
}
