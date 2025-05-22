<?php

namespace App\Enums\Content;

use App\Traits\EnumTrait;

enum BannerType: int
{
    use EnumTrait;

    case HOME = 1; // 首頁輪播
    case LOGIN = 2; // 登入頁
    case EVENT = 3; // 活動頁
    case SHOP = 4; // 商城專區
    case PAYMENT = 5; // 金流專區
    case GAME = 6; // 遊戲專區
    case MEMBER = 7; // 會員中心
}
