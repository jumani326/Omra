<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByRole = config('roles.permissions', []);
        $allPermissions = array_unique(array_merge(...array_values($permissionsByRole)));

        foreach ($allPermissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Supprimer les anciens rôles (ne garder que les 4 rôles)
        $allowedRoles = array_keys(config('roles.roles', []));
        Role::query()->whereNotIn('name', $allowedRoles)->delete();

        // Créer les 4 rôles et assigner les permissions
        foreach ($permissionsByRole as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
    }
}
