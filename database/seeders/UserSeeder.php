<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kira',
            'email' => 'kira@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        User::create([
            'name' => 'Jotaro',
            'email' => 'jotaro@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
        User::create([
            'name' => 'Dio',
            'email' => 'dio@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
