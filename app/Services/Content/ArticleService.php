<?php

namespace App\Services\Content;

use App\Repositories\Content\ArticleRepository;

class ArticleService
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->articleRepository->search($search)
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
        $this->articleRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->articleRepository->getEntity() :
        $this->articleRepository->relation(['tags', 'category'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->articleRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->articleRepository->delete($id);
    }
}
