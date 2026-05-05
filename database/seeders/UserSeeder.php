<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'teste@moodup.com'
        ], [
            'name' => 'Usuário Teste',
            'password' => Hash::make('12345678'),
        ]);

        User::factory(3)->create(); // mais usuários aleatórios
    }
}