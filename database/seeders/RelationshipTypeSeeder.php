<?php

namespace Database\Seeders;

use App\Models\RelationshipType;
use Illuminate\Database\Seeder;

class RelationshipTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Dean',
            'Program Head',
            'Faculty Member',
            'Adviser/Class Adviser',
            'Staff',
            'Parent',
            'Guardian',
            'Classmate',
            'Self-Referral',
            'Other',
        ];

        foreach ($types as $name) {
            RelationshipType::firstOrCreate(['name' => $name]);
        }
    }
}