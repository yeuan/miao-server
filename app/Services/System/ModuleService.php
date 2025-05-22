<?php

namespace App\Services\System;

use App\Repositories\System\ModuleRepository;

class ModuleService
{
    public function __construct(
        protected ModuleRepository $moduleRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'sort');

        $result = $this->moduleRepository->search($search)->order($order)->result();

        return [
            'result' => $result,
            'refer' => [
                'status' => \App\Enums\Status::toObject(),
            ],
        ];
    }

    public function update(array $row, int $id): void
    {
        // 過濾參數
        $row = filterRequest($row);
        $this->moduleRepository->update($row, $id);
    }

    public function getActiveModules(): array
    {
        return $this->moduleRepository->getActiveModules();
    }
}
