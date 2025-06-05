<?php

namespace App\Http\Requests\Admin\System;

use App\Http\Requests\BaseRequest;

class CleanUnusedUploadsRequest extends BaseRequest
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
        return [
            'minutes' => $this->intWithMaxRule(config('custom.length.clean_unused_uploads.minutes_max')),
        ];
    }
}
