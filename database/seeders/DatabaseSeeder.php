<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TodoEntry;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RegistersTableSeeder::class);
        $this->call(NotesTableSeeder::class);
        $this->call(ToDoEntryTableSeeder::class);
        $this->call(CategoryTagsTableSeeder::class);
        $this->call(ImageTableSeeder::class);




    }
}
