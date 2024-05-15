<?php

namespace App\Http\Controllers;

use App\Models\CategoryTag;
use App\Models\Register;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    // Gibt alle Tags zurück
    public function index(): JsonResponse
    {
        // Lädt alle Tags aus der CategoryTag-Modell
        $tags = CategoryTag::all();
        // Gibt die Tags als JSON-Antwort zurück
        return response()->json($tags, 200);
    }

    // Findet ein einzelnes Tag anhand seiner ID und gibt es zurück
    public function findById(int $id): JsonResponse
    {
        // Sucht das erste Tag, das der ID entspricht
        $tag = CategoryTag::where('id', $id)->first();
        // Gibt das gefundene Tag zurück oder null, wenn keines gefunden wurde
        return $tag != null ? response()->json($tag, 200) : response()->json(null, 200);
    }

    // Findet Tags anhand eines Suchbegriffs und gibt sie zurück
    public function findBySearchTerm(string $searchTerm): JsonResponse
    {
        // Sucht Tags, deren Kategorie den Suchbegriff enthält
        $tags = CategoryTag::where('category', 'LIKE', '%' . $searchTerm . '%')->get();
        // Gibt die gefundenen Tags zurück
        return response()->json($tags, 200);
    }

    // Speichert ein neues Tag basierend auf den Daten der Anfrage
    public function save(Request $request): JsonResponse
    {
        // Parse und formatiere das Datum der Anfrage
        $request = $this->parseRequest($request);
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Erstelle ein neues Tag mit den Daten aus der Anfrage
            $tag = CategoryTag::create($request->all());
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Gibt das neu erstellte Tag zurück
            return response()->json($tag, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating tag failed: " . $e->getMessage(), 420);
        }
    }

    // Funktion, um eine Anfrage zu parsen und das Datum zu formatieren
    private function parseRequest(Request $request): Request
    {
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

    // Aktualisiert ein Tag basierend auf den Daten der Anfrage und der Tag-ID
    public function update(Request $request, int $id): JsonResponse
    {
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Finde das erste Tag mit der gegebenen ID
            $tag = CategoryTag::where('id', $id)->first();
            if ($tag != null) {
                // Parse die Anfrage und aktualisiere das Tag
                $request = $this->parseRequest($request);
                $tag->update($request->all());
                // Speichere das Tag
                $tag->save();
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Lade das aktualisierte Tag
            $tag1 = CategoryTag::where('id', $id)->first();
            return response()->json($tag1, 201);

        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);
        }
    }

    // Löscht ein Tag anhand seiner ID
    public function delete(int $id): JsonResponse
    {
        // Finde das erste Tag mit der gegebenen ID
        $tag = CategoryTag::where('id', $id)->first();
        if ($tag != null) {
            // Lösche das Tag
            $tag->delete();
            return response()->json('tag (' . $id . ') successfully deleted', 200);
        }
        else {
            // Wenn kein Tag gefunden wurde, gebe einen Fehler zurück
            return response()->json('tag could not be deleted - it does not exist', 422);
        }
    }

}
