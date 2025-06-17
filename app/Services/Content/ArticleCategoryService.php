<?php

namespace App\Services\Content;

use App\Enums\ApiCode;
use App\Exceptions\Api\ApiException;
use App\Repositories\Content\ArticleCategoryRepository;
use App\Repositories\Content\ArticleRepository;

class ArticleCategoryService
{
    public function __construct(
        protected ArticleCategoryRepository $articleCategoryRepository,
        protected ArticleRepository $articleRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->articleCategoryRepository->search($search)
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
        $this->articleCategoryRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->articleCategoryRepository->getEntity() :
        $this->articleCategoryRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->articleCategoryRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        // 防呆：有子分類不可刪
        if ($this->articleCategoryRepository->whereExists(['pid' => $id])) {
            throw new ApiException(ApiCode::HAS_CHILDREN_DATA->name);
        }

        // 防呆：有文章不可刪
        if ($this->articleRepository->whereExists(['category_id' => $id])) {
            throw new ApiException(ApiCode::HAS_ARTICLES_DATA->name);
        }

        $this->articleCategoryRepository->delete($id);
    }
}
