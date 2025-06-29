<?php

namespace App\Services\Content;

use App\Repositories\Content\BannerRepository;

class BannerService
{
    public function __construct(
        protected BannerRepository $bannerRepository
    ) {}

    public function list(array $input): array
    {
        ['order' => $order, 'search' => $search] = paramProcess($input, 'id');

        $result = $this->bannerRepository->search($search)
            ->relation(['tags'])
            ->order($order)->paginate($input['per_page'] ?? config('custom.default.per_page'))
            ->result();

        return [
            'result' => $result,
            'refer' => [
                'type' => \App\Enums\Content\BannerType::toObject(),
                'flag' => \App\Enums\Content\BannerFlag::toObject(),
                'status' => \App\Enums\Status::toObject(),
                'link_type' => \App\Enums\Content\BannerLinkType::toObject(),
                'owner_type' => \App\Enums\Common\OwnerType::toObject(),
            ],
        ];
    }

    public function store(array $row): void
    {
        $this->bannerRepository->create($row);
    }

    public function show(int $id): object
    {
        // id 為 0 時代表取得空白表單範本資料
        return $id == 0 ?
        $this->bannerRepository->getEntity() :
        $this->bannerRepository->relation(['tags'])->row($id);
    }

    public function update(array $row, int $id): void
    {
        $this->bannerRepository->update($row, $id);
    }

    public function destroy(int $id): void
    {
        $this->bannerRepository->delete($id);
    }
}
