<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DateTimeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (empty($value)) {
            return null;
        }

        $timeZoneSource = new \DateTimeZone(date_default_timezone_get());
        $timeZoneTarget = new \DateTimeZone(config('app.timezone'));

        $dateTime = is_numeric($value)
            ? (new \DateTime('@'.$value))->setTimezone($timeZoneSource)
            : new \DateTime($value, $timeZoneSource);

        $dateTime->setTimezone($timeZoneTarget);

        return $dateTime->format(config('custom.default.datetime', 'Y-m-d H:i:s'));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_numeric($value) ? $value : strtotime($value);
    }
}
