<?php

namespace App\Traits;

trait EnumTrait
{
    /**
     * 取得翻譯鍵
     */
    protected function getLocalizationKey(): string
    {
        return 'enums.'.static::class.'.'.$this->name;
    }

    /**
     * 取得翻譯內容
     */
    public function label(): string
    {
        return trans($this->getLocalizationKey());
    }

    /**
     * 靜態方法：嘗試從名稱取得Enum
     */
    public static function tryFromName(string $name): ?self
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    public static function toObject(): object
    {
        return (object) static::toArray();
    }
}
