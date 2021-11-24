<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoyenReglementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('moyen_reglements')->insert([
            [
                'libelle_moyen_reglement' => "CARTE DE FIDELITE",
                'created_at' => now()
            ],
            [
                'libelle_moyen_reglement' => "ESPECE",
                'created_at' => now()
            ]
        ]);
    }
}
