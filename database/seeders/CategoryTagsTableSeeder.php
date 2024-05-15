<?php

namespace Database\Seeders;

use App\Models\CategoryTag;
use App\Models\TodoEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryTag1 = new CategoryTag();
        $categoryTag1->category = 'dringend';
        $categoryTag1->save();

        $categoryTag2 = new CategoryTag();
        $categoryTag2->category = 'sehr dringend';
        $categoryTag2->save();

        $categoryTag3 = new CategoryTag();
        $categoryTag3->category = 'wenn Zeit bleibt';
        $categoryTag3->save();
    }
}
