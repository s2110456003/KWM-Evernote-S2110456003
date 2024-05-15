<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Note;
use App\Models\TodoEntry;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToDOEntryController extends Controller
{
    // Gibt alle Todo-Einträge zusammen mit ihren Bildern und Benutzern zurück
    public function index(): JsonResponse
    {
        // Lädt alle Todo-Einträge und deren Bilder und Benutzer
        $todo_entries = TodoEntry::with(['images', 'user'])->get();
        // Gibt die Todo-Einträge als JSON-Antwort zurück
        return response()->json($todo_entries, 200);
    }

    // Findet einen einzelnen Todo-Eintrag anhand seiner ID und gibt ihn zurück
    public function findById(int $id): JsonResponse
    {
        // Sucht den ersten Todo-Eintrag, der der ID entspricht
        $todo_entry = TodoEntry::where('id', $id)->with(['images', 'user'])->first();
        // Gibt den gefundenen Todo-Eintrag zurück oder null, wenn keiner gefunden wurde
        return $todo_entry != null ? response()->json($todo_entry, 200) : response()->json(null, 200);
    }

    // Überprüft, ob ein Todo-Eintrag mit einer bestimmten ID existiert
    public function checkId(int $id): JsonResponse
    {
        // Sucht den ersten Todo-Eintrag, der der ID entspricht
        $todo_entry = TodoEntry::where('id', $id)->first();
        // Gibt true zurück, wenn der Todo-Eintrag existiert, sonst false
        return $todo_entry != null ? response()->json(true, 200) : response()->json(false, 200);
    }

    // Findet Todo-Einträge anhand eines Suchbegriffs und gibt sie zurück
    public function findBySearchTerm(string $searchTerm): JsonResponse
    {
        // Sucht Todo-Einträge, deren Titel oder Beschreibung den Suchbegriff enthalten
        $todo_entries = TodoEntry::with(['images', 'user'])
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')->get();
        // Gibt die gefundenen Todo-Einträge zurück
        return response()->json($todo_entries, 200);
    }

    // Speichert einen neuen Todo-Eintrag basierend auf den Daten der Anfrage
    public function save(Request $request): JsonResponse
    {
        // Parse und formatiere das Datum der Anfrage
        $request = $this->parseRequest($request);
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Erstelle einen neuen Todo-Eintrag mit den Daten aus der Anfrage
            $todo_entry = TodoEntry::create($request->all());

            // Speichere die Bilder des Todo-Eintrags
            if (isset($request['images']) && is_array($request['images'])) {
                foreach ($request['images'] as $img) {
                    $image = Image::firstOrNew(['url' => $img['url'], 'title' => $img['title']]);
                    $todo_entry->images()->save($image);
                }
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Gibt den neu erstellten Todo-Eintrag zurück
            return response()->json($todo_entry, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating todo_entry failed: " . $e->getMessage(), 420);
        }
    }

    // Funktion, um eine Anfrage zu parsen und das Datum zu formatieren
    private function parseRequest(Request $request): Request
    {
        // Konvertiere das eingehende Datum aus ISO 8601 Format zu 'Y-m-d H:i:s'
        $date = new DateTime($request->due_date);
        $request['due_date'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

    // Aktualisiert einen Todo-Eintrag basierend auf den Daten der Anfrage und der Todo-ID
    public function update(Request $request, int $id): JsonResponse
    {
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Finde den ersten Todo-Eintrag mit der gegebenen ID
            $todo_entry = TodoEntry::with(['images', 'user'])
                ->where('id', $id)->first();
            if ($todo_entry != null) {
                // Parse die Anfrage und aktualisiere den Todo-Eintrag
                $request = $this->parseRequest($request);
                $todo_entry->update($request->all());

                // Lösche alte Bilder und speichere die neuen
                $todo_entry->images()->delete();
                if (isset($request['images']) && is_array($request['images'])) {
                    foreach ($request['images'] as $img) {
                        $image = Image::firstOrNew(['url' => $img['url'], 'title' => $img['title']]);
                        $todo_entry->images()->save($image);
                    }
                }
                // Speichere den aktualisierten Todo-Eintrag
                $todo_entry->save();
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Lade den aktualisierten Todo-Eintrag
            $todo_entry1 = TodoEntry::with(['images', 'user'])
                ->where('id', $id)->first();
            return response()->json($todo_entry1, 201);

        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating TodoEntry failed: " . $e->getMessage(), 420);
        }
    }

    // Löscht einen Todo-Eintrag anhand seiner ID
    public function delete(int $id): JsonResponse
    {
        // Finde den ersten Todo-Eintrag mit der gegebenen ID
        $todo_entry = TodoEntry::where('id', $id)->first();
        if ($todo_entry != null) {
            // Lösche den Todo-Eintrag
            $todo_entry->delete();
            return response()->json('TodoEntry (' . $id . ') successfully deleted', 200);
        }
        else {
            // Wenn kein Todo-Eintrag gefunden wurde, gebe einen Fehler zurück
            return response()->json('TodoEntry could not be deleted - it does not exist', 422);
        }
    }
}
