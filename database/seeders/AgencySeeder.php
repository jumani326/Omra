<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        // Créer une agence test
        $agency = Agency::create([
            'name' => 'Agence Omra Test',
            'license_no' => 'LIC-2024-001',
            'ministry_status' => 'approved',
            'contact' => [
                'phone' => '+212 6 12 34 56 78',
                'email' => 'contact@agence-omra-test.ma',
                'address' => '123 Avenue Mohammed V, Casablanca, Maroc',
            ],
            'logo' => null,
        ]);

        // Créer une branche principale
        $branch = Branch::create([
            'agency_id' => $agency->id,
            'name' => 'Branche Principale Casablanca',
            'address' => '123 Avenue Mohammed V, Casablanca, Maroc',
            'phone' => '+212 6 12 34 56 78',
            'manager_id' => null, // Sera assigné après création admin
        ]);

        $this->command->info("Agence créée : {$agency->name} (ID: {$agency->id})");
        $this->command->info("Branche créée : {$branch->name} (ID: {$branch->id})");
    }
}
