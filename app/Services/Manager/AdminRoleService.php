<?php

namespace App\Services\Manager;

use App\Repositories\Manager\AdminRoleRepository;

class AdminRoleService
{
    public function __construct(
        protected AdminRoleRepository $adminRoleRepository,
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->adminRoleRepository->search($search)
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        // 過濾參數
        $row = filterRequest($row);
        $row['allow_nav'] = is_array($row['allow_nav']) ? $row['allow_nav'] : json_decode($row['allow_nav'], true);
        $this->adminRoleRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->adminRoleRepository->getEntity() :
        $this->adminRoleRepository->row($id);
    }

    public function update(array $row, int $id): void
    {
        // 過濾參數
        $row = filterRequest($row);
        $row['allow_nav'] = is_array($row['allow_nav']) ? $row['allow_nav'] : json_decode($row['allow_nav'], true);
        $this->adminRoleRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->adminRoleRepository->delete($id);
    }
}
