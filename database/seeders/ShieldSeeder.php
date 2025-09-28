<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use BezhanSalleh\FilamentShield\Support\Utils;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus cache permission dari Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================
        // Pilihan: Uncomment baris ini
        // untuk generate permission default Filament sekali saja
        // supaya permission default tersedia. Tapi nggak wajib:
        // Artisan::call('shield:generate', ['--all' => true], $this->command->getOutput());
        // ============================

        // JSON definisi role + permission
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
              "restore_any_inventory", "replicate_inventory", "reorder_inventory", "delete_inventory", "delete_any_inventory",
              "force_delete_inventory", "force_delete_any_inventory",

              "view_lab::usage", "view_any_lab::usage", "create_lab::usage", "update_lab::usage", "restore_lab::usage",
              "restore_any_lab::usage", "replicate_lab::usage", "reorder_lab::usage", "delete_lab::usage",
              "delete_any_lab::usage", "force_delete_lab::usage", "force_delete_any_lab::usage",

              "view_maintenance", "view_any_maintenance", "create_maintenance", "update_maintenance", "restore_maintenance",
              "restore_any_maintenance", "replicate_maintenance", "reorder_maintenance", "delete_maintenance", "delete_any_maintenance",
              "force_delete_maintenance", "force_delete_any_maintenance",

              "view_shield::role", "view_any_shield::role", "create_shield::role", "update_shield::role", "delete_shield::role", "delete_any_shield::role",

              "view_user", "view_any_user", "create_user", "update_user", "restore_user", "restore_any_user",
              "replicate_user", "reorder_user", "delete_user", "delete_any_user", "force_delete_user", "force_delete_any_user",

              "widget_ChartsDuo", "widget_StatsOverview", "widget_BlogPostsChart", "widget_BlogPosts2Chart",

              "page_EditProfilePage", "page_Themes",

              "export_category", "export_inventory", "export_borrow", "export_maintenance", "export_lab::usage", "import_inventory", "access_log"
            ]
          },
          {
            "name": "guru",
            "guard_name": "web",
            "permissions": [
              "view_borrow", "create_borrow", "view_any_borrow",
              "view_any_inventory",
              "view_lab::usage", "view_any_lab::usage", "update_lab::usage"
            ]
          },
          {
            "name": "siswa",
            "guard_name": "web",
            "permissions": [
              "view_borrow", "create_borrow", "view_any_borrow",
              "view_any_inventory"
            ]
          }
        ]';

        $directPermissionsJson = '[]';

        $roles = json_decode($rolesWithPermissions, true);
        $directPermissions = json_decode($directPermissionsJson, true);

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => $roleData['guard_name'],
            ]);

            foreach ($roleData['permissions'] as $permName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => $roleData['guard_name'],
                ]);

                $role->givePermissionTo($permission);
            }
        }

        foreach ($directPermissions as $permName) {
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('ShieldSeeder: roles & permissions seeded.');

        // Assign super_admin ke user pertama
        $firstUser = User::orderBy('id', 'asc')->first();
        if ($firstUser) {
            $firstUser->assignRole('super_admin');
            $this->command->warn("Assigned super_admin role to user {$firstUser->email}");
        } else {
            $this->command->error('No user found to assign super_admin!');
        }
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