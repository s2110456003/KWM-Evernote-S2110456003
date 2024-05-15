<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Annahme, dass User und Notes bereits existieren
        $users = User::all();
        $notes = Note::all();

        // Bilder für User
        foreach ($users as $user) {
            $user->images()->create([
                'url' => 'https://picsum.photos/200/300',
            ]);
        }

        // Bilder für Notes
        foreach ($notes as $note) {
            $note->images()->create([
                'url' => 'path/to/note/image.jpg'
            ]);
        }

        // Falls keine spezifische Zuordnung benötigt wird
        Image::create([
            'url' => 'https://picsum.photos/seed/picsum/200/300',
            'imageable_id' => null,
            'imageable_type' => null
        ]);
    }
}
