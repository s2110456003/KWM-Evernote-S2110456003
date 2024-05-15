<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Note extends Model
{
    use HasFactory;

    // Die Attribute, die beim Massen-Zuweisen zugewiesen werden können
    protected $fillable = ['title', 'description', 'image', 'user_id', 'register_id', 'tag_id'];

    // Definiert eine polymorphe Beziehung zu Bildern
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Definiert eine inverse Beziehung zu einem User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Definiert eine many-to-many Beziehung zu Registern
    public function registers(): BelongsToMany
    {
        return $this->belongsToMany(Register::class, 'note_register','note_id', 'register_id');
    }

    // Definiert eine one-to-many Beziehung zu TodoEntries
    public function todoEntries(): HasMany
    {
        return $this->hasMany(TodoEntry::class, 'note_id');
    }

    // Definiert eine inverse Beziehung zu einem Tag
    public function tags(): BelongsToMany
    {
        // Hier ist die Korrektur: Verwenden von 'category_tag_id' statt 'tag_id'
        return $this->belongsToMany(CategoryTag::class, 'category_tag_note', 'note_id', 'category_tag_id');
    }
}
