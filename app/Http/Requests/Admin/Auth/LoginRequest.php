<?php

namespace App\Http\Requests\Admin\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'username' => 'bail|'.$this->stringRule(0, true),
            'password' => 'bail|'.$this->stringRule(0, true),
        ];

        // 驗證碼啟用設定
        if (config('custom.settings.verification.use_admin_login_validate', false)) {
            if ($field = getVerificationField()) {
                $rules[$field] = 'bail|'.$this->stringRule(0, true);
            }
        }

        return $rules;
    }
}
