<?php

namespace App\Models;

use App\Http\Controllers\ToDOEntryController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Register extends Model
{
    use HasFactory; // Benutzt die Laravel Eloquent Model Factory Trait für einfache Erstellung von Model-Instanzen

    // Die Attribute, die beim Massen-Zuweisen zugewiesen werden können
    protected $fillable = ['title']; // Erlaubt Massenzuweisung für das 'title' Attribut

    // Definiert eine many-to-many Beziehung zu Notes
    public function notes(): BelongsToMany
    {
        // Die notes() Methode definiert eine BelongsToMany-Beziehung zwischen Register und Note
        // 'note_register' ist der Name der Pivot-Tabelle, die Register und Note verbindet
        return $this->belongsToMany(Note::class, 'note_register', 'register_id', 'note_id');
    }


}
