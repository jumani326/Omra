<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agency;
use App\Models\Branch;
use App\Models\Group;
use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée les utilisateurs de test pour les 4 rôles.
     * Ministère : créé automatiquement (pas d'inscription publique).
     * Agence : liée à une agence (validée ou en attente).
     */
    public function run(): void
    {
        $agency = Agency::first();
        $branch = Branch::first();

        // ——— MINISTÈRE (créé automatiquement) ———
        User::firstOrCreate(
            ['email' => 'ministere@omra.test'],
            [
                'name' => 'Ministère',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        )->assignRole('ministere');

        $this->command->info('Ministère créé : ministere@omra.test (Mot de passe: password)');

        if (! $agency || ! $branch) {
            $this->command->warn('Aucune agence/branche. Utilisateurs agence/guide non créés.');
            return;
        }

        // ——— AGENCE ———
        $agenceUser = User::firstOrCreate(
            ['email' => 'agence@omra.test'],
            [
                'name' => 'Admin Agence',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'agence_id' => $agency->id,
                'active' => true,
            ]
        );
        if (! $agenceUser->hasRole('agence')) {
            $agenceUser->assignRole('agence');
        }
        $this->command->info('Agence créée : agence@omra.test (Mot de passe: password)');

        // ——— GUIDE (créé par l'agence, appartient à une agence) ———
        $guideUser = User::firstOrCreate(
            ['email' => 'guide@omra.test'],
            [
                'name' => 'Guide Test',
                'password' => Hash::make('password'),
                'agence_id' => $agency->id,
                'active' => true,
            ]
        );
        if (! $guideUser->hasRole('guide')) {
            $guideUser->assignRole('guide');
        }
        $group = Group::firstOrCreate(['agency_id' => $agency->id, 'name' => 'Groupe 1']);
        Guide::firstOrCreate(
            ['user_id' => $guideUser->id],
            ['user_id' => $guideUser->id, 'agency_id' => $agency->id, 'group_id' => $group->id]
        );
        $this->command->info('Guide créé : guide@omra.test (Mot de passe: password)');

        // ——— PÈLERIN ———
        $pelerinUser = User::firstOrCreate(
            ['email' => 'pelerin@omra.test'],
            [
                'name' => 'Pèlerin Test',
                'password' => Hash::make('password'),
                'active' => true,
                'activated_at' => now(),
            ]
        );
        if (! $pelerinUser->hasRole('pelerin')) {
            $pelerinUser->assignRole('pelerin');
        }
        $this->command->info('Pèlerin créé : pelerin@omra.test (Mot de passe: password)');
    }
}
