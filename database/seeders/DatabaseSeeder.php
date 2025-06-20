<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Administrador'],
            ['name' => 'Resguardante'],
            ['name' => 'Operador'],
        ]);
    }
}
