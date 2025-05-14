<?php

namespace App\Http\Resources\Manager;

use App\Http\Resources\SuccessResource;

class ProfileResource extends SuccessResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'role_name' => $this->role->name,
            'login_count' => $this->login_count,
            'login_ip' => $this->login_ip,
            'login_time' => $this->formatDateTime($this->login_time),
            'status' => $this->status,
            'token' => $this->token,
            'backstage' => $this->backstage ?? requestOutParam('backstage'),
            'created_at' => (string) $this->created_at,
        ];
    }
}
