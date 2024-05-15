<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TodoEntry extends Model
{
    use HasFactory; // Benutzt die Laravel Eloquent Model Factory Trait für einfache Erstellung von Model-Instanzen

    // Die Attribute, die beim Massen-Zuweisen zugewiesen werden können
    protected $fillable = ['title', 'description', 'due_date', 'image', 'user_id', 'note_id'];
    // Erlaubt Massenzuweisung für diese Attribute

    // Definiert eine many-to-one Beziehung zu User
    public function user(): BelongsTo
    {
        // Die user() Methode definiert eine BelongsTo-Beziehung zwischen TodoEntry und User
        // Dies bedeutet, dass jeder TodoEntry genau einen User besitzt
        return $this->belongsTo(User::class);
    }

    // Definiert eine one-to-many Polymorphe Beziehung zu Images
    public function images(): MorphMany
    {
        // Die images() Methode definiert eine MorphMany-Beziehung zwischen TodoEntry und Image
        // 'imageable' ist der MorphName, der verwendet wird, um die polymorphe Beziehung zu identifizieren
        return $this->morphMany(Image::class, 'imageable');
    }


    // Definiert eine many-to-one Beziehung zu Note
    public function note(): BelongsTo
    {
        // Die note() Methode definiert eine BelongsTo-Beziehung zwischen TodoEntry und Note
        // 'note_id' ist der Fremdschlüssel in der TodoEntry-Tabelle, der die Verbindung zu Note herstellt
        return $this->belongsTo(Note::class, 'note_id');
    }
}
