<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'testuser';
        $user->email = 'test@email.com';
        $user->password = 'secret';
        $user->save();

        $user2 = new User();
        $user2->name = 'Frida';
        $user2->email = 'frida@email.com';
        $user2->password = bcrypt('secret');
        $user2->save();

        $user3 = new User();
        $user3->name = 'Kurt';
        $user3->email = 'kurt@email.com';
        $user3->password = bcrypt('secret');
        $user3->save();
    }
}
