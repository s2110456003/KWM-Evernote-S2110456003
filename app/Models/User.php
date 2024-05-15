<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable; // Verwendet mehrere Traits, um zusätzliche Funktionen bereitzustellen:

    // HasApiTokens: Ermöglicht die Verwendung von API-Tokens mit dem Sanctum-Paket für API-Authentifizierung.
    // HasFactory: Erlaubt die Verwendung von Model Factories für einfache Generierung von Testdaten.
    // Notifiable: Ermöglicht die Verwendung von Notifications in der Anwendung.

    /**
     * Die Attribute, die bei Massenzuweisungen zugewiesen werden können.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Der Name des Benutzers
        'email', // Die E-Mail-Adresse des Benutzers
        'password', // Das Passwort des Benutzers
    ];

    /**
     * Die Attribute, die bei der JSON-Serialisierung ausgeblendet werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Das Passwort sollte niemals sichtbar sein.
        'remember_token', // Das "remember me" Token für Sessions
    ];

    /**
     * Die Attribute, die automatisch konvertiert/typisiert werden sollen.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Wandelt das 'email_verified_at' in ein DateTime-Objekt um.
        'password' => 'hashed', // Anmerkung: In der Praxis wird 'password' nicht in $casts gesetzt.
    ];

    /**
     * Definiert eine one-to-many Beziehung zu Note.
     *
     * @return HasMany
     */
    public function notes() : HasMany{
        // Jeder User kann mehrere Notes besitzen.
        return $this->hasMany(Note::class);
    }

    /**
     * Definiert eine polymorphe one-to-many Beziehung zu Images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        // Jeder User kann mehrere Images besitzen, identifiziert durch 'imageable'.
        return $this->morphMany('App\Models\Image', 'imageable');
    }

    /**
     * Gibt den Schlüssel zurück, der zur Identifizierung des Users dient.
     *
     * @return mixed
     */
    public function getJWTIdentifier(){
        // Die Methode wird von JWTAuth verwendet, um den User eindeutig zu identifizieren.
        return $this->getKey();
    }

    /**
     * Gibt ein Array an benutzerdefinierten Claims zurück, die dem JWT hinzugefügt werden sollen.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // Hier können zusätzliche Informationen/Claims hinzugefügt werden, die im JWT-Token enthalten sein sollen.
        return ['user'=>['id'=>$this->id]];
    }
}
