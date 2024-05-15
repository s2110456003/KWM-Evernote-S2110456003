<?php

// Definiere den Namespace des Models, der angibt, wo sich die Klasse im Projekt befindet
namespace App\Models;

// Importiere die notwendigen Laravel-Komponenten und -Traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Image extends Model
{
    // Verwende die HasFactory-Trait, um Factory-Methoden für das Modell zu ermöglichen.
    // Dies ist hilfreich für die Erstellung von Datenbankeinträgen in Tests.
    use HasFactory;


    protected $fillable = ['url', 'title'];


    public function imageable()
    {
        // Die morphTo-Methode definiert eine polymorphe Beziehung in Laravel.
        // Dies bedeutet, dass das Bild zu verschiedenen Modellen gehören kann,
        // je nachdem, wie es in der Datenbank verwendet wird.
        return $this->morphTo();
    }

}
