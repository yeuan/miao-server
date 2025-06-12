<?php

namespace App\Services\Content;

use App\Repositories\Content\NoticeRepository;

class NoticeService
{
    public function __construct(
        protected NoticeRepository $noticeRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->noticeRepository->search($search)
            ->relation(['tags'])
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
                'type' => \App\Enums\Content\NoticeType::toObject(),
                'flag' => \App\Enums\Content\NoticeFlag::toObject(),
                'owner_type' => \App\Enums\Common\OwnerType::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->noticeRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->noticeRepository->getEntity() :
        $this->noticeRepository->relation(['tags'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->noticeRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->noticeRepository->delete($id);
    }
}
