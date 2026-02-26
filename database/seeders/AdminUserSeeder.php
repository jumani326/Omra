<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer la première branche
        $branch = Branch::first();

        if (!$branch) {
            $this->command->error('Aucune branche trouvée. Exécutez d\'abord AgencySeeder.');
            return;
        }

        // Créer Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@omra.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'active' => true,
        ]);
        $superAdmin->assignRole('Super Admin Agence');

        // Mettre à jour le manager de la branche
        $branch->update(['manager_id' => $superAdmin->id]);

        $this->command->info("Super Admin créé : {$superAdmin->email} (Mot de passe: password)");

        // Créer Admin Branche
        $adminBranche = User::create([
            'name' => 'Admin Branche',
            'email' => 'admin.branche@omra.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'active' => true,
        ]);
        $adminBranche->assignRole('Admin Branche');

        $this->command->info("Admin Branche créé : {$adminBranche->email} (Mot de passe: password)");
    }
}
