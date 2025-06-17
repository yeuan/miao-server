<?php

namespace App\Services\Content;

use App\Enums\ApiCode;
use App\Exceptions\Api\ApiException;
use App\Repositories\Content\FaqCategoryRepository;
use App\Repositories\Content\FaqRepository;

class FaqCategoryService
{
    public function __construct(
        protected FaqCategoryRepository $faqCategoryRepository,
        protected FaqRepository $faqRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->faqCategoryRepository->search($search)
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
        $this->faqCategoryRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->faqCategoryRepository->getEntity() :
        $this->faqCategoryRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->faqCategoryRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        // 防呆：有子分類不可刪
        if ($this->faqCategoryRepository->whereExists(['pid' => $id])) {
            throw new ApiException(ApiCode::HAS_CHILDREN_DATA->name);
        }

        // 防呆：有文章不可刪
        if ($this->faqRepository->whereExists(['category_id' => $id])) {
            throw new ApiException(ApiCode::HAS_ARTICLES_DATA->name);
        }

        $this->faqCategoryRepository->delete($id);
    }
}
