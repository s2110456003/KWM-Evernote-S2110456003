<?php

use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ToDOEntryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;


// Route für '/user' mit 'auth:sanctum' Middleware für API-Authentifizierung
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // Gibt den authentifizierten Benutzer zurück
    return $request->user();
});


// Note-Routen

// Route, die alle Notizen zurückgibt
Route::get('notes', [NoteController::class,'index']);

// Route, die eine spezifische Notiz durch ihre ID zurückgibt
Route::get('notes/{id}', [NoteController::class,'findById']);

// Route, die prüft, ob eine Notiz-ID existiert
Route::get('notes/checkId/{id}', [NoteController::class,'checkId']);

// Route, die Notizen anhand eines Suchbegriffs findet
Route::get('notes/search/{searchTerm}', [NoteController::class,'findBySearchTerm']);


// Todo-Entry-Routen

// Route, die alle Todo-Einträge zurückgibt
Route::get('todo_entries', [ToDOEntryController::class,'index']);

// Route, die einen spezifischen Todo-Eintrag durch seine ID zurückgibt
Route::get('todo_entries/{id}', [ToDOEntryController::class,'findById']);

// Route, die prüft, ob eine Todo-Eintrag-ID existiert
Route::get('todo_entries/checkId/{id}', [ToDOEntryController::class,'checkId']);

// Route, die Todo-Einträge anhand eines Suchbegriffs findet
Route::get('todo_entries/search/{searchTerm}', [ToDOEntryController::class,'findBySearchTerm']);


// Register-Routen

// Route, die alle Register zurückgibt
Route::get('registers', [RegisterController::class,'index']);

// Route, die ein spezifisches Register durch seine ID zurückgibt
Route::get('registers/{id}', [RegisterController::class,'findById']);

// Route, die prüft, ob eine Register-ID existiert
Route::get('registers/checkId/{id}', [RegisterController::class,'checkId']);

// Route, die Register anhand eines Suchbegriffs findet
Route::get('registers/search/{searchTerm}', [RegisterController::class,'findBySearchTerm']);


// Tag-Routen

// Route, die alle Tags zurückgibt
Route::get('tags', [TagController::class,'index']);

// Route, die einen spezifischen Tag durch seine ID zurückgibt
Route::get('tags/{id}', [TagController::class,'findById']);

// Route, die Tags anhand eines Suchbegriffs findet
Route::get('tags/search/{searchTerm}', [TagController::class,'findBySearchTerm']);


// User-Routen

// Route, die alle Benutzer zurückgibt
Route::get('users', [UserController::class,'index']);

// Route, die einen spezifischen Benutzer durch seine ID zurückgibt
Route::get('users/{id}', [UserController::class,'findById']);


// Authentifizierungs-Routen

// Route für Benutzer-Login
Route::post('auth/login',[\App\Http\Controllers\AuthController::class,'login']);


// Routen, die eine 'api' und 'auth.jwt' Middleware für geschützte API-Endpunkte verwenden

Route::group(['middleware'=>['api', 'auth.jwt']], function () {
    // Post-Routen
    Route::post('notes', [NoteController::class, 'save']); // Speichert eine neue Notiz
    Route::post('todo_entries', [ToDOEntryController::class, 'save']); // Speichert einen neuen Todo-Eintrag
    Route::post('registers', [RegisterController::class, 'save']); // Speichert ein neues Register
    Route::post('tags', [TagController::class, 'save']); // Speichert einen neuen Tag
    Route::post('users', [UserController::class, 'save']); // Speichert einen neuen Benutzer

    // Put-Routen
    Route::put('notes/{id}', [NoteController::class, 'update']); // Aktualisiert eine Notiz
    Route::put('todo_entries/{id}', [ToDOEntryController::class, 'update']); // Aktualisiert einen Todo-Eintrag
    Route::put('registers/{id}', [RegisterController::class, 'update']); // Aktualisiert ein Register
    Route::put('tags/{id}', [TagController::class, 'update']); // Aktualisiert einen Tag
    Route::put('users/{id}', [UserController::class, 'update']); // Aktualisiert einen Benutzer

    // Delete-Routen
    Route::delete('notes/{id}', [NoteController::class,'delete']); // Löscht eine Notiz
    Route::delete('todo_entries/{id}', [ToDOEntryController::class,'delete']); // Löscht einen Todo-Eintrag
    Route::delete('registers/{id}', [RegisterController::class,'delete']); // Löscht ein Register
    Route::delete('tags/{id}', [TagController::class,'delete']); // Löscht einen Tag
    Route::delete('users/{id}', [UserController::class,'delete']); // Löscht einen Benutzer

    // Spezielle Routen
    Route::post('registers/{id}/assign-notes', [RegisterController::class, 'assignNotes']); // Weist Notizen einem Register zu
    Route::post('/notes/{note}/tags', [NoteController::class, 'assignTag']);

    // Logout-Route
    Route::post('auth/logout', [\App\Http\Controllers\AuthController::class,'logout']); // Benutzer-Logout
});
