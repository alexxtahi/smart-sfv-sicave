<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'full_name' => "Concepteur de l'application",
            'email' => 'Concepteur@app.com',
            'login' => 'Concepteur@app.com',
            'contact' => '00000000',
            'role' => 'Concepteur',
            'password' => bcrypt('P@ssword@123456'),
            'created_at' => now()
        ]);

        DB::table('users')->insert([
            'full_name' => "Alexandre TAHI",
            'email' => 'alexandretahi7@gmail.com',
            'login' => 'alexandretahi7@gmail.com',
            'contact' => '(05) 84-64-98-25',
            'role' => 'Concepteur',
            'password' => bcrypt('alex-smarty'),
            'created_at' => now()
        ]);
    }
}
