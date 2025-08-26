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
        {"name":"super_admin","guard_name":"web","permissions":["view_borrow","view_any_borrow","create_borrow","update_borrow","restore_borrow","restore_any_borrow","replicate_borrow","reorder_borrow","delete_borrow","delete_any_borrow","force_delete_borrow","force_delete_any_borrow","view_category::item","view_any_category::item","create_category::item","update_category::item","restore_category::item","restore_any_category::item","replicate_category::item","reorder_category::item","delete_category::item","delete_any_category::item","force_delete_category::item","force_delete_any_category::item","view_item","view_any_item","create_item","update_item","restore_item","restore_any_item","replicate_item","reorder_item","delete_item","delete_any_item","force_delete_item","force_delete_any_item","view_item::borrow","view_any_item::borrow","create_item::borrow","update_item::borrow","restore_item::borrow","restore_any_item::borrow","replicate_item::borrow","reorder_item::borrow","delete_item::borrow","delete_any_item::borrow","force_delete_item::borrow","force_delete_any_item::borrow","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","widget_AvailableItemsWidget"]},
        
        {"name":"siswa","guard_name":"web","permissions":["view_borrow","create_borrow","view_any_borrow","view_item"]},

        {"name":"guru","guard_name":"web","permissions":["view_borrow","create_borrow","view_any_borrow","view_item", "view_lab::usage", "view_any_lab::usage", "create_lab::usage"]}
        
        ]';
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