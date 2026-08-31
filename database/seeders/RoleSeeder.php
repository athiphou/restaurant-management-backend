<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Role::insert([
            ['name' => 'Admin', 'description' => 'System Administrator'],
            ['name' => 'Manager', 'description' => 'Branch Manager'],
            ['name' => 'Waiter', 'description' => 'Service Staff'],
            ['name' => 'Customer', 'description' => 'App Customer'],
        ]);
    }
}
