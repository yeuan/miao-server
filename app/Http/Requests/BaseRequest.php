<?php

namespace App\Http\Requests;

use App\Enums\ApiCode;
use App\Traits\RequestRulesTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class BaseRequest extends FormRequest
{
    use RequestRulesTrait;

    /**
     * 是否合併 route 參數至驗證資料中
     */
    protected bool $mergeRouteParams = true;

    /**
     * 各子類必須定義 rules
     */
    abstract public function rules(): array;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 抓取route設定的參數
     */
    public function validationData()
    {
        return $this->mergeRouteParams
        ? array_merge(parent::validationData(), $this->route()?->parameters() ?? []) // 同時抓取route設定的參數及傳入的參數
        : parent::validationData();
    }

    protected function failedValidation(Validator $validator): void
    {
        // 使用 ErrorResource 處理驗證錯誤
        throw respondError(ApiCode::VALIDATION_PARAMS_INVALID->name, new ValidationException($validator))
            ->additional([
                'validation' => $validator->errors()->first(),
                'errors' => $validator->getMessageBag(),
            ])
            ->response()
            ->throwResponse();
    }
}
