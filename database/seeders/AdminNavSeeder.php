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
        $allowReserve = AdminNavFlag::ALLOW_RESERVE->value; // 預留 -> 4
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
            ],
            '網站管理' => [
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
            ],
        ];

        $entries = [];

        $walk = function ($tree) use (&$walk, &$entries, &$id, $now, $by) {
            foreach ($tree as $groupName => $modules) {
                $groupId = $id++;
                $entries[] = $this->navRow($groupId, 0, '', $groupName, '', 0, '', $now, $by);

                foreach ($modules as $module) {
                    $moduleId = $id++;
                    $moduleCode = $module['module_code'] ?? '';
                    $entries[] = $this->navRow($moduleId, $groupId, "$groupId", $module['name'], $module['route'], $module['flag'], $moduleCode, $now, $by);

                    foreach ($module['children'] ?? [] as $child) {
                        $entries[] = $this->navRow($id++, $moduleId, "$groupId-$moduleId", $child['name'], $child['route'], $child['flag'], '', $now, $by);
                    }
                }
            }
        };

        $walk($tree);

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
