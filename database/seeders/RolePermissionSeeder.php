<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Dashboard
            'view-dashboard',
            'view-dashboard-ministry',
            
            // Pèlerins
            'view-pilgrims',
            'create-pilgrims',
            'edit-pilgrims',
            'delete-pilgrims',
            'export-pilgrims',
            
            // Forfaits
            'view-packages',
            'create-packages',
            'edit-packages',
            'delete-packages',
            
            // Visas
            'view-visas',
            'create-visas',
            'edit-visas',
            'delete-visas',
            
            // Finance
            'view-payments',
            'create-payments',
            'edit-payments',
            'view-commissions',
            'view-financial-reports',
            
            // Utilisateurs
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Agences
            'view-agencies',
            'create-agencies',
            'edit-agencies',
            'approve-agencies',
            
            // Chatbot
            'configure-chatbot',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles
        
        // SPHÈRE MINISTÈRE
        $superviseurMinistry = Role::create(['name' => 'Superviseur Ministériel National']);
        $superviseurMinistry->syncPermissions([
            'view-dashboard-ministry',
            'view-pilgrims',
            'view-payments',
            'view-visas',
            'view-agencies',
            'approve-agencies',
        ]);

        $auditeur = Role::create(['name' => 'Auditeur National']);
        $auditeur->syncPermissions([
            'view-dashboard-ministry',
            'view-pilgrims',
            'view-payments',
            'view-visas',
            'view-agencies',
        ]);

        // SPHÈRE AGENCE
        $superAdmin = Role::create(['name' => 'Super Admin Agence']);
        $superAdmin->syncPermissions($permissions);

        $adminBranche = Role::create(['name' => 'Admin Branche']);
        $adminBranche->syncPermissions([
            'view-dashboard',
            'view-pilgrims', 'create-pilgrims', 'edit-pilgrims', 'delete-pilgrims', 'export-pilgrims',
            'view-packages', 'create-packages', 'edit-packages', 'delete-packages',
            'view-visas', 'create-visas', 'edit-visas', 'delete-visas',
            'view-payments', 'create-payments', 'edit-payments',
            'view-commissions', 'view-financial-reports',
            'view-users', 'create-users', 'edit-users',
        ]);

        $agentCommercial = Role::create(['name' => 'Agent Commercial']);
        $agentCommercial->syncPermissions([
            'view-dashboard',
            'view-pilgrims', 'create-pilgrims', 'edit-pilgrims',
            'view-packages',
            'view-visas',
            'view-payments',
        ]);

        $comptable = Role::create(['name' => 'Comptable']);
        $comptable->syncPermissions([
            'view-dashboard',
            'view-pilgrims',
            'view-payments', 'create-payments', 'edit-payments',
            'view-commissions', 'view-financial-reports',
        ]);

        $officierVisa = Role::create(['name' => 'Officier Visa']);
        $officierVisa->syncPermissions([
            'view-dashboard',
            'view-pilgrims',
            'view-visas', 'create-visas', 'edit-visas', 'delete-visas',
        ]);

        $guide = Role::create(['name' => 'Guide / Mourchid']);
        $guide->syncPermissions([
            'view-dashboard',
            'view-pilgrims',
        ]);

        // SPHÈRE CLIENT
        $pilgrim = Role::create(['name' => 'Pèlerin (Client)']);
        $pilgrim->syncPermissions([
            'view-dashboard',
        ]);
    }
}
