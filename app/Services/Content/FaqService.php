<?php

namespace App\Services\Content;

use App\Repositories\Content\FaqRepository;

class FaqService
{
    public function __construct(
        protected FaqRepository $newsRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->newsRepository->search($search)
            ->relation(['tags', 'category'])
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'flag' => \App\Enums\Content\ContentFlag::toObject(),
                'status' => \App\Enums\Status::toObject(),
                'owner_type' => \App\Enums\Common\OwnerType::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->newsRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->newsRepository->getEntity() :
        $this->newsRepository->relation(['tags', 'category'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->newsRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->newsRepository->delete($id);
    }
}
