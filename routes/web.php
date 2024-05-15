<?php

use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Definiere eine Route für die Basis-URL ('/')
Route::get('/', function () {
    // Hole alle Einträge aus der 'notes'-Tabelle und speichere sie in der Variable 'notes'
    $notes = Note::all();

    // Gebe die View 'notes.index' zurück und übergebe die Variable 'notes'
    // an die View mit Hilfe der Funktion compact()
    return view('notes.index', compact('notes'));
});

// Definiere eine Route für die URL '/notes'
Route::get('/notes', [\App\Http\Controllers\NoteController::class, "index"]);

// Definiere eine Route für '/notes/{id}', wobei '{id}' ein Platzhalter für die spezifische ID einer Notiz ist
Route::get('/notes/{id}', function ($id) {
    // Finde die Notiz mit der angegebenen ID und speichere sie in der Variable 'note'
    $note = Note::find($id);

    // Gebe die View 'notes.show' zurück und übergebe die Variable 'note'
    // an die View mit Hilfe der Funktion compact()
    return view('notes.show', compact('note'));
});

// Definiere eine Route für die URL '/todo_entries'
Route::get('/todo_entries', [\App\Http\Controllers\ToDOEntryController::class, "index"]);
