<?php

namespace App\Http\Controllers;

use App\Models\CategoryTag;
use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Gibt alle Benutzer zurück
    public function index(): JsonResponse
    {
        // Lädt alle Benutzer aus der Datenbank
        $users = User::all();
        // Gibt die Benutzer als JSON-Antwort zurück
        return response()->json($users, 200);
    }

    // Findet einen einzelnen Benutzer anhand seiner ID
    public function findById(int $id): JsonResponse
    {
        // Sucht den Benutzer mit der spezifischen ID
        $user = User::where('id', $id)->first();
        // Gibt den Benutzer zurück, oder null, wenn kein Benutzer gefunden wurde
        return $user != null ? response()->json($user, 200) : response()->json(null, 200);
    }

    // Speichert einen neuen Benutzer basierend auf den Daten der Anfrage
    public function save(Request $request): JsonResponse
    {
        // Parse und formatiere das Datum der Anfrage
        $request = $this->parseRequest($request);
        // Startet eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Erstellt einen neuen Benutzer mit den Daten aus der Anfrage
            $user = User::create($request->all());
            // Bestätigt die Datenbank-Transaktion
            DB::commit();
            // Gibt den neu erstellten Benutzer zurück
            return response()->json($user, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating register failed: " . $e->getMessage(), 420);
        }
    }

    // Hilfsfunktion, um das Datum in der Anfrage zu formatieren
    private function parseRequest(Request $request): Request
    {
        // Konvertiert das Datum von ISO 8601 Format zu 'Y-m-d H:i:s'
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

    // Aktualisiert einen Benutzer basierend auf den Daten der Anfrage und der Benutzer-ID
    public function update(Request $request, int $id): JsonResponse
    {
        // Startet eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Sucht den Benutzer mit der spezifischen ID
            $user = User::where('id', $id)->first();
            if ($user != null) {
                // Parse die Anfrage und aktualisiert den Benutzer
                $request = $this->parseRequest($request);
                $user->update($request->all());
                // Speichert den aktualisierten Benutzer
                $user->save();
            }
            // Bestätigt die Datenbank-Transaktion
            DB::commit();
            // Lädt den aktualisierten Benutzer
            $user1 = User::where('id', $id)->first();
            return response()->json($user1, 201);

        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating user failed: " . $e->getMessage(), 420);
        }
    }

    // Löscht einen Benutzer anhand seiner ID
    public function delete(int $id): JsonResponse
    {
        // Sucht den Benutzer mit der spezifischen ID
        $user = User::where('id', $id)->first();
        if ($user != null) {
            // Löscht den Benutzer
            $user->delete();
            return response()->json('user (' . $id . ') successfully deleted', 200);
        }
        else {
            // Wenn kein Benutzer gefunden wurde, gebe einen Fehler zurück
            return response()->json('user could not be deleted - it does not exist', 422);
        }
    }
}
