<?php

namespace App\Services\Content;

use App\Enums\ApiCode;
use App\Exceptions\Api\ApiException;
use App\Repositories\Content\NewsCategoryRepository;
use App\Repositories\Content\NewsRepository;

class NewsCategoryService
{
    public function __construct(
        protected NewsCategoryRepository $newsCategoryRepository,
        protected NewsRepository $newsRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->newsCategoryRepository->search($search)
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
        $this->newsCategoryRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->newsCategoryRepository->getEntity() :
        $this->newsCategoryRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->newsCategoryRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        // 防呆：有子分類不可刪
        if ($this->newsCategoryRepository->whereExists(['pid' => $id])) {
            throw new ApiException(ApiCode::HAS_CHILDREN_DATA->name);
        }

        // 防呆：有文章不可刪
        if ($this->newsRepository->whereExists(['category_id' => $id])) {
            throw new ApiException(ApiCode::HAS_ARTICLES_DATA->name);
        }

        $this->newsCategoryRepository->delete($id);
    }
}
