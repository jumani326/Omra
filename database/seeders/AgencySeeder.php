<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $contact = [
            'phone' => '+212 6 12 34 56 78',
            'email' => 'contact@agence-omra-test.ma',
            'address' => '123 Avenue Mohammed V, Casablanca, Maroc',
        ];

        $agency = Agency::firstOrCreate(
            ['license_no' => 'LIC-2024-001'],
            [
                'name' => 'Agence Omra Test',
                'ministry_status' => 'approved',
                'validated' => true,
                'contact' => $contact,
                'logo' => null,
            ]
        );

        $branch = Branch::firstOrCreate(
            ['agency_id' => $agency->id, 'name' => 'Branche Principale Casablanca'],
            [
                'agency_id' => $agency->id,
                'name' => 'Branche Principale Casablanca',
                'address' => '123 Avenue Mohammed V, Casablanca, Maroc',
                'phone' => '+212 6 12 34 56 78',
                'manager_id' => null,
            ]
        );

        $this->command->info("Agence : {$agency->name} (ID: {$agency->id})");
        $this->command->info("Branche : {$branch->name} (ID: {$branch->id})");
    }
}
