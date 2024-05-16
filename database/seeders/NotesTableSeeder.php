<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DateTime;
use Illuminate\Support\Facades\DB;

class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $note = new Note();
        $note->title = 'Vatertag';
        $note->description = 'Geschenk für Papa besorgen';

        $user = User::inRandomOrder()->first();
        $note->user()->associate($user);
        $note->save();

        // Bild zur Notiz hinzufügen
        $image = new Image([
            'url' => 'https://picsum.photos/seed/picsum/200/300',
            'title' => 'Bild3'
        ]);
        $note->images()->save($image); // Verknüpfe das Bild mit der Notiz

        $note2 = new Note();
        $note2->title = 'Muttertag';
        $note2->description = 'Gedicht schreiben';

        $user2 = User::inRandomOrder()->first();
        $note2->user()->associate($user2);

        $note2->save();

        // Bild zur Notiz hinzufügen
        $image2 = new Image([
            'url' => 'https://picsum.photos/seed/picsum/200/300',
            'title' => 'Bild2'
        ]);
        $note2->images()->save($image2); // Verknüpfe das Bild mit der Notiz

        $note3 = new Note();
        $note3->title = 'Mona Geburtstag';
        $note3->description = 'Fressnapf Gutscheine';

        $user3 = User::inRandomOrder()->first();
        $note3->user()->associate($user3);

        $note3->save();

        // Bild zur Notiz hinzufügen
        $image3 = new Image([
            'url' => 'https://picsum.photos/seed/picsum/200/300',
            'title' => 'Bild3'
        ]);
        $note3->images()->save($image3); // Verknüpfe das Bild mit der Notiz
    }
}
