<?php

namespace App\Rules;

use App\Repositories\Manager\AdminRoleRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BackstageRoleMatchRule implements ValidationRule
{
    /**
     * 設定後台類型
     */
    public function __construct(
        private int $backstage,
    ) {}

    /**
     * 驗證 role_id 對應 backstage 是否一致
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $role = $this->findRole($value);

        if (! $role || ! $this->isBackstageMatch($role['backstage'])) {
            $fail('The :attribute is invalid. The selected role must belong to the same backstage type as the current admin.');
        }
    }

    /**
     * 查詢對應角色
     *
     * @param  int  $roleId
     */
    private function findRole($roleId): ?array
    {
        return app(AdminRoleRepository::class)->rowArray($roleId);
    }

    /**
     * 檢查 backstage 是否符合
     *
     * @param  int  $roleBackstage
     */
    private function isBackstageMatch($roleBackstage): bool
    {
        return $roleBackstage === $this->backstage;
    }
}
