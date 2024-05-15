<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Konstruktor der Klasse AuthController
    public function __construct(){
        // Authentifizierungsmiddleware für alle Routen in diesem Controller,
        // außer für die 'login'-Route.
        $this->middleware('auth:api', ['except' =>['login']]);
    }

    // Methode für den Login-Prozess
    public function login(){
        // Speichert die Anmeldedaten ('email' und 'password') aus der Anfrage
        $credentials = request(['email', 'password']);

        // Versucht, sich mit den bereitgestellten Anmeldedaten zu authentifizieren
        $token = auth()->attempt($credentials);

        // Überprüft, ob der Authentifizierungsversuch fehlgeschlagen ist
        if(!$token){
            // Wenn kein Token zurückgegeben wird, sende eine 401 Unauthorized Antwort
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Wenn die Authentifizierung erfolgreich war, sende das Token zurück
        return $this->respondWithToken($token);
    }

    // Hilfsfunktion, die das Token in einer JSON-Struktur zurückgibt
    private function respondWithToken($token){
        return response()->json([
            'access_token' => $token, // Das Token selbst
            'token_type' => 'bearer', // Der Typ des Tokens, hier Bearer
            // Die Lebensdauer des Tokens in Minuten
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    // Methode für den Logout-Prozess
    public function logout(){
        // Loggt den aktuellen Benutzer aus (invalidiert das Token)
        auth()->logout();

        // Sendet eine Bestätigung, dass der Logout erfolgreich war
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Methode zum Erneuern eines abgelaufenen oder bald ablaufenden Tokens
    public function refresh()
    {
        // Erneuert das Token und gibt das neue Token zurück
        return $this->respondWithToken(auth()->refresh());
    }

}
