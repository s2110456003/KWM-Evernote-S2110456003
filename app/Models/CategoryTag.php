<?php

namespace App\Models;

// Importiere die notwendigen Klassen für das Modell
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Definiere die Klasse CategoryTag, die von Model erbt
class CategoryTag extends Model
{
    // Nutze die HasFactory-Trait, um Factory-Methoden für das Modell zu ermöglichen
    use HasFactory;

    // $fillable definiert, welche Attribute massenweise zugewiesen werden können
    protected $fillable = ['category'];

    // Definiere eine öffentliche Methode `notes`, die die Beziehung zu vielen Notizen (Note) definiert
    public function notes(): BelongsToMany
    {
        // Korrektur: Verwenden von 'category_tag_id' statt 'tag_id'
        return $this->belongsToMany(Note::class, 'category_tag_note', 'category_tag_id', 'note_id');
    }
}
