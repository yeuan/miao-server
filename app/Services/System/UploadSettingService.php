<?php

namespace App\Services\System;

use App\Repositories\System\UploadSettingRepository;

class UploadSettingService
{
    public function __construct(
        protected UploadSettingRepository $uploadSettingRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'sort');

        $result = $this->uploadSettingRepository->search($search)->order($order)->result();

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
                'type' => \App\Enums\System\UploadType::toObject(),
                'thumb_mode' => \App\Enums\System\ThumbMode::toObject(),
            ],
        ];
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->uploadSettingRepository->getEntity() :
        $this->uploadSettingRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        // 過濾參數
        $row = filterRequest($row);
        $this->uploadSettingRepository->update($row, $id);
    }
}
