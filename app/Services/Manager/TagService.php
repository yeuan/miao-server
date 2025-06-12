<?php

namespace App\Services\Manager;

use App\Repositories\Manager\TagRepository;

class TagService
{
    public function __construct(
        protected TagRepository $tagRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->tagRepository->search($search)
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
        $this->tagRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->tagRepository->getEntity() :
        $this->tagRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->tagRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->tagRepository->delete($id);
    }
}
