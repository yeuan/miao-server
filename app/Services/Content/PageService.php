<?php

namespace App\Services\Content;

use App\Repositories\Content\PageRepository;

class PageService
{
    public function __construct(
        protected PageRepository $pageRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->pageRepository->search($search)
            ->relation(['tags'])
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
                'owner_type' => \App\Enums\Common\OwnerType::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->pageRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->pageRepository->getEntity() :
        $this->pageRepository->relation(['tags'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->pageRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->pageRepository->delete($id);
    }
}
