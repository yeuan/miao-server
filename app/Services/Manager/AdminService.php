<?php

namespace App\Services\Manager;

use App\Repositories\Manager\AdminRepository;
use App\Repositories\Manager\AdminRoleRepository;

class AdminService
{
    public function __construct(
        protected AdminRepository $adminRepository,
        protected AdminRoleRepository $adminRoleRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->adminRepository->search($search)
            ->relation(['role'])
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'role' => $this->adminRoleRepository->getRoleList(true),
                'status' => \App\Enums\Status::toObject(),
                'backstage' => \App\Enums\Backstage::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->adminRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->adminRepository->getEntity() :
        $this->adminRepository->relation(['role'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->adminRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->adminRepository->delete($id);
    }
}
