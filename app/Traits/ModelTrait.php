<?php

namespace App\Traits;

trait ModelTrait
{
    public function getCreatedAtAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }

    public function getUpdatedAtAttribute($rawTime): string
    {
        return $this->changeTimeZone($rawTime, '', config('app.timezone'));
    }

    /**
     * 一次設多個屬性（不會存進資料庫）
     */
    public function setAttributes(array $attributes): static
    {
        collect($attributes)->each(fn ($value, $key) => $this->{$key} = $value);

        return $this;
    }

    private function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null): string
    {
        $timeZoneSource = new \DateTimeZone($timeZoneSource ?: date_default_timezone_get());
        $timeZoneTarget = new \DateTimeZone($timeZoneTarget ?: date_default_timezone_get());

        $dateTime = is_numeric($dateString)
        ? (new \DateTime('@'.$dateString))->setTimezone($timeZoneSource)
        : new \DateTime($dateString, $timeZoneSource);

        $dateTime->setTimezone($timeZoneTarget);

        return $dateTime->format(config('custom.default.datetime', 'Y-m-d H:i:s'));
    }
}
