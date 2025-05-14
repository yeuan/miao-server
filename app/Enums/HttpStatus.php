<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum HttpStatus: int
{
    use EnumTrait;

    case SUCCESS = 200;
    case REQUEST_FAILED = 400;
    case AUTH_ERROR = 401;
    case NOT_FOUND = 404;
    case PARAMS_ERROR = 422;
    case SERVER_ERROR = 500;
    case SERVICE_UNAVAILABLE = 503;
}
