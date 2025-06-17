<?php

namespace Database\Seeders;

use App\Enums\Manager\AdminNavFlag;
use App\Models\Manager\AdminNav;
use Illuminate\Database\Seeder;

class AdminNavSeeder extends Seeder
{
    public function run(): void
    {
        $now = time();
        $by = 'System';
        $id = 1;

        $allowBackstage = AdminNavFlag::ALLOW_BACKSTAGE->value; // 允許總後台 -> 1
        $allowAgentBackstage = AdminNavFlag::ALLOW_AGENT_BACKSTAGE->value; // 允許代理後台 -> 2
        $allowReserve = AdminNavFlag::ALLOW_TENANT_BACKSTAGE->value; // 允許多租客後台 -> 4
        $actionRecord = AdminNavFlag::ACTION_RECORD->value; // 操作記錄 -> 8
        $final = AdminNavFlag::FINAL->value; // 最後一層 -> 16

        // 層級結構資料
        $tree = [
            '系統管理' => [
                [
                    'name' => '管理者角色列表',
                    'route' => 'admin_role.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [
                        ['name' => '詳情',  'route' => 'admin_role.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'admin_role.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'admin_role.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'admin_role.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '管理者列表',
                    'route' => 'admin.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [
                        ['name' => '詳情',  'route' => 'admin.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'admin.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'admin.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'admin.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '後台導航列表',
                    'route' => 'admin_nav.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [
                        ['name' => '詳情',  'route' => 'admin_nav.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'admin_nav.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'admin_nav.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'admin_nav.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '模組列表',
                    'route' => 'modules.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [
                        ['name' => '修改',  'route' => 'modules.update',  'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '標籤管理',
                    'route' => 'tags.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [
                        ['name' => '詳情',  'route' => 'tags.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'tags.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'tags.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'tags.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
            ],
            '內容管理' => [
                [
                    'name' => '公告列表',
                    'route' => 'notice.index',
                    'flag' => $allowBackstage | $final,
                    'module_code' => 'notice',
                    'children' => [
                        ['name' => '詳情',  'route' => 'notice.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'notice.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'notice.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'notice.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '輪播圖列表',
                    'route' => 'banner.index',
                    'flag' => $allowBackstage | $final,
                    'module_code' => 'banner',
                    'children' => [
                        ['name' => '詳情',  'route' => 'banner.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'banner.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'banner.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'banner.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '單頁列表',
                    'route' => 'page.index',
                    'flag' => $allowBackstage | $final,
                    'module_code' => 'page',
                    'children' => [
                        ['name' => '詳情',  'route' => 'page.show',   'flag' => $allowBackstage | $final],
                        ['name' => '新增',  'route' => 'page.store',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'page.update',  'flag' => $allowBackstage | $final],
                        ['name' => '刪除',  'route' => 'page.destroy', 'flag' => $allowBackstage | $final],
                    ],
                ],
                [
                    'name' => '文章',
                    'route' => '',
                    'flag' => $allowBackstage,
                    'children' => [
                        [
                            'name' => '文章列表',
                            'route' => 'article.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'article',
                            'children' => [
                                ['name' => '詳情',  'route' => 'article.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'article.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'article.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'article.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                        [
                            'name' => '文章分類',
                            'route' => 'article_category.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'article_category',
                            'children' => [
                                ['name' => '詳情',  'route' => 'article_category.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'article_category.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'article_category.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'article_category.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '最新消息',
                    'route' => '',
                    'flag' => $allowBackstage,
                    'children' => [
                        [
                            'name' => '最新消息列表',
                            'route' => 'news.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'news',
                            'children' => [
                                ['name' => '詳情',  'route' => 'news.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'news.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'news.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'news.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                        [
                            'name' => '最新消息分類',
                            'route' => 'news_category.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'news_category',
                            'children' => [
                                ['name' => '詳情',  'route' => 'news_category.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'news_category.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'news_category.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'news_category.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => '常見問題',
                    'route' => '',
                    'flag' => $allowBackstage,
                    'children' => [
                        [
                            'name' => '常見問題列表',
                            'route' => 'faq.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'faq',
                            'children' => [
                                ['name' => '詳情',  'route' => 'faq.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'faq.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'faq.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'faq.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                        [
                            'name' => '常見問題分類',
                            'route' => 'faq_category.index',
                            'flag' => $allowBackstage | $final,
                            'module_code' => 'faq_category',
                            'children' => [
                                ['name' => '詳情',  'route' => 'faq_category.show',   'flag' => $allowBackstage | $final],
                                ['name' => '新增',  'route' => 'faq_category.store',  'flag' => $allowBackstage | $final],
                                ['name' => '修改',  'route' => 'faq_category.update', 'flag' => $allowBackstage | $final],
                                ['name' => '刪除',  'route' => 'faq_category.destroy', 'flag' => $allowBackstage | $final],
                            ],
                        ],
                    ],
                ],
            ],
            '系統設定' => [
                [
                    'name' => '上傳設置',
                    'route' => 'upload_settings.index',
                    'flag' => $allowBackstage | $final,
                    'children' => [

                        ['name' => '詳情',  'route' => 'upload_settings.show',   'flag' => $allowBackstage | $final],
                        ['name' => '修改',  'route' => 'upload_settings.update',  'flag' => $allowBackstage | $final],
                        ['name' => '圖片上傳',  'route' => 'upload.image',   'flag' => $allowBackstage | $final],
                    ],
                ],
            ],
        ];

        $entries = [];

        $walk = function ($modules, $pid = 0, $path = '') use (&$walk, &$entries, &$id, $now, $by) {
            foreach ($modules as $module) {
                // 分群組（字串）或模組（陣列）
                if (is_string($module)) {
                    // 忽略，通常是分群組才會有
                    continue;
                }
                $curId = $id++;
                $moduleCode = $module['module_code'] ?? '';
                $route = $module['route'] ?? '';
                $flag = $module['flag'] ?? 0;
                $name = $module['name'] ?? '';
                $entries[] = $this->navRow($curId, $pid, $path, $name, $route, $flag, $moduleCode, $now, $by);

                if (isset($module['children']) && is_array($module['children']) && count($module['children'])) {
                    $walk($module['children'], $curId, $path === '' ? "$curId" : "$path-$curId");
                }
            }
        };

        // 處理每一個分組（第一層）
        foreach ($tree as $groupName => $modules) {
            $groupId = $id++;
            $entries[] = $this->navRow($groupId, 0, '', $groupName, '', 0, '', $now, $by);
            $walk($modules, $groupId, (string) $groupId);
        }

        foreach ($entries as $e) {
            AdminNav::updateOrInsert(['id' => $e['id']], $e);
        }
    }

    private function navRow($id, $pid, $path, $name, $route, $flag, $module_code, $now, $by): array
    {
        return [
            'id' => $id,
            'pid' => $pid,
            'path' => $path,
            'name' => $name,
            'route' => $route,
            'flag' => $flag,
            'module_code' => $module_code,
            'created_by' => $by,
            'updated_by' => $by,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
