<?php

namespace App\Http\Resources\Manager;

use App\Http\Resources\SuccessResource;

class AdminResource extends SuccessResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'role_name' => optional($this->whenLoaded('role'))->name ?? '',
            'login_ip' => $this->login_ip,
            'login_time' => $this->formatDateTime($this->login_time),
            'login_count' => $this->login_count,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
