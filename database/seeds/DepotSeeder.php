<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('depots')->insert([
            'libelle_depot' => "Tous les dépôts",
            'adresse_depot' => 'Tous',
            'created_at' => now(),
        ]);
    }
}
