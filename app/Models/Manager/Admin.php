<?php

namespace App\Models\Manager;

use App\Casts\DateTimeCast;
use App\Traits\ModelTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use ModelTrait;

    protected $table = 'admin';

    protected $dateFormat = 'U';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => DateTimeCast::class,
        'updated_at' => DateTimeCast::class,
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Manager\AdminRole', 'role_id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
