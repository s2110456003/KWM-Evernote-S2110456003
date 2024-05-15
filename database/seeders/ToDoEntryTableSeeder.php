<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\TodoEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToDoEntryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $note = Note::inRandomOrder()->first();  // Hol eine zufällige Note aus der DB

        $toDoEntry = new TodoEntry();
        $toDoEntry->title = 'FH';
        $toDoEntry->description = 'KWM-Evernote erstellen';
        $toDoEntry->due_date = '2024-04-15';
        $toDoEntry->note_id = $note ? $note->id : null;  // Setze note_id, falls eine Note vorhanden ist

        $user = User::inRandomOrder()->first();
        $toDoEntry->user()->associate($user);
        $toDoEntry->save();


        $toDoEntry2 = new TodoEntry();
        $toDoEntry2->title = 'FH';
        $toDoEntry2->description = 'Bachelorarbeit schreiben';
        $toDoEntry2->due_date = '2024-05-16';
        $toDoEntry2->note_id = 2;

        $user2 = User::inRandomOrder()->first();
        $toDoEntry2->user()->associate($user2);
        $toDoEntry2->save();

        $toDoEntry3 = new TodoEntry();
        $toDoEntry3->title = 'Privates';
        $toDoEntry3->description = 'Lesen';
        $toDoEntry3->due_date = '2024-04-19';
        $toDoEntry3->note_id = 3;

        $user3 = User::inRandomOrder()->first();
        $toDoEntry3->user()->associate($user3);
        $toDoEntry3->save();
    }
}
