<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $register1 = new \App\Models\Register;
        $register1->title = "Geschenke";
        $register1->save();

        $register2 = new \App\Models\Register;
        $register2->title = "FH Hagenberg";
        $register2->save();

        $register3 = new \App\Models\Register;
        $register3->title = "Haushalt";
        $register3->save();
    }
}
