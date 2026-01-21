<?php

namespace Database\Seeders;

use App\Models\Administration\User\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Gender::create(['title' => 'Masculino']);
        Gender::create(['title' => 'Feminino']);
        Gender::create(['title' => 'Prefiro não informar']);
    }
}
