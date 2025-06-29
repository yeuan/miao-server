<?php

namespace App\Services\Manager;

use App\Enums\ApiCode;
use App\Exceptions\Api\ApiException;
use App\Repositories\Manager\AdminNavRepository;

class AdminNavService
{
    public function __construct(
        protected AdminNavRepository $adminNavRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'sort');

        $navList = $this->adminNavRepository->search($search)->order($order)->result();

        $result = $this->adminNavRepository->treeNav($navList);

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
                'flag' => \App\Enums\Manager\AdminNavFlag::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->adminNavRepository->create($row);
    }

    public function show(int $id): object
    {
        $row = $id == 0 ?
        $this->adminNavRepository->getEntity() :
        $this->adminNavRepository->row($id);
        $parent = $this->adminNavRepository->rowArray($row['pid'] ?? 0);
        $row['pname'] = $parent['name'] ?? '';

        return $row;
    }

    public function update(array $row, int $id): void
    {
        $this->adminNavRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        // 防呆：有子分類不可刪
        if ($this->adminNavRepository->whereExists(['pid' => $id])) {
            throw new ApiException(ApiCode::HAS_CHILDREN_DATA->name);
        }

        $this->adminNavRepository->delete($id);
    }

    public function getSidebar()
    {
        return $this->adminNavRepository->getNavTree(requestOutParam('backstage'));
    }
}
