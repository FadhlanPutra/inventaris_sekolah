<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[
      {
        "name": "super_admin",
        "guard_name": "web",
        "permissions": [
          "view_borrow", "view_any_borrow", "create_borrow", "update_borrow", "restore_borrow",
          "restore_any_borrow", "replicate_borrow", "reorder_borrow", "delete_borrow", "delete_any_borrow",
          "force_delete_borrow", "force_delete_any_borrow",

          "view_category", "view_any_category", "create_category", "update_category", "restore_category",
          "restore_any_category", "replicate_category", "reorder_category", "delete_category", "delete_any_category",
          "force_delete_category", "force_delete_any_category",

          "view_inventory", "view_any_inventory", "create_inventory", "update_inventory", "restore_inventory",
          "restore_any_inventory", "replicate_inventory", "reorder_inventory", "delete_inventory", "delete_any_inventory", "force_delete_inventory", "force_delete_any_inventory",

          "view_lab::usage", "view_any_lab::usage", "create_lab::usage", "update_lab::usage", "restore_lab::usage",
          "restore_any_lab::usage", "replicate_lab::usage", "reorder_lab::usage", "delete_lab::usage",
          "delete_any_lab::usage", "force_delete_lab::usage", "force_delete_any_lab::usage",

          "view_maintenance", "view_any_maintenance", "create_maintenance", "update_maintenance", "restore_maintenance", "restore_any_maintenance", "replicate_maintenance", "reorder_maintenance", "delete_maintenance", "delete_any_maintenance", "force_delete_maintenance", "force_delete_any_maintenance",

          "view_role", "view_any_role", "create_role", "update_role", "delete_role", "delete_any_role",

          "view_user", "view_any_user", "create_user", "update_user", "restore_user", "restore_any_user",
          "replicate_user", "reorder_user", "delete_user", "delete_any_user", "force_delete_user", "force_delete_any_user",

          "widget_ChartsDuo", "widget_StatsOverview", "widget_BlogPostsChart", "widget_BlogPosts2Chart"
        ]
      },
      {
        "name": "siswa",
        "guard_name": "web",
        "permissions": [
          "view_borrow", "create_borrow", "view_any_borrow",
          "view_inventory", "view_maintenance"
        ]
      },
      {
        "name": "guru",
        "guard_name": "web",
        "permissions": [
          "view_borrow", "create_borrow", "view_any_borrow",
          "view_inventory", "view_maintenance",
          "view_lab::usage", "view_any_lab::usage", "update_lab::usage"
        ]
      }
    ]
    ';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}