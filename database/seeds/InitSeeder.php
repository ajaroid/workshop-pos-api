<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'jarjit',
            'email' => 'jarjit@mail.com',
            'password' => Hash::make('123456'),
            'api_token' => Str::random(60),
        ]);
    }
}
