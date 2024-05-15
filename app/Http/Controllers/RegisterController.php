<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Note;
use App\Models\TodoEntry;
use App\Models\Register;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    // Gibt alle Register zusammen mit ihren zugeordneten Notizen zurück
    public function index(): JsonResponse
    {
        // Lädt alle Register und ihre zugehörigen Notizen
        $registers = Register::with(['notes'])->get();
        // Gibt die Register als JSON-Antwort zurück
        return response()->json($registers, 200);
    }

    // Findet ein einzelnes Register anhand seiner ID und gibt es zusammen mit seinen Notizen zurück
    public function findById(int $id): JsonResponse
    {
        // Sucht das erste Register, das der ID entspricht, zusammen mit seinen Notizen
        $register = Register::where('id', $id)->with(['notes'])->first();
        // Gibt das gefundene Register zurück oder null, wenn keines gefunden wurde
        return $register != null ? response()->json($register, 200) : response()->json(null, 200);
    }

    // Überprüft, ob ein Register mit einer bestimmten ID existiert
    public function checkId(int $id): JsonResponse
    {
        // Sucht das erste Register, das der ID entspricht
        $register = Register::where('id', $id)->first();
        // Gibt true zurück, wenn das Register gefunden wurde, sonst false
        return $register != null ? response()->json(true, 200) : response()->json(false, 200);
    }

    // Findet Register anhand eines Suchbegriffs und gibt sie zusammen mit ihren Notizen zurück
    public function findBySearchTerm(string $searchTerm): JsonResponse
    {
        // Sucht Register, deren Titel den Suchbegriff enthält, zusammen mit ihren Notizen
        $registers = Register::with(['notes'])
            ->where('title', 'LIKE', '%' . $searchTerm . '%')->get();
        // Gibt die gefundenen Register zurück
        return response()->json($registers, 200);
    }

    // Speichert ein neues Register basierend auf den Daten der Anfrage
    public function save(Request $request): JsonResponse
    {
        // Parse und formatiere das Datum der Anfrage
        $request = $this->parseRequest($request);
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Erstelle ein neues Register mit den Daten aus der Anfrage
            $register = Register::create($request->all());
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Gibt das neu erstellte Register zurück
            return response()->json($register, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating register failed: " . $e->getMessage(), 420);
        }
    }

    // Funktion, um eine Anfrage zu parsen und das Datum zu formatieren
    private function parseRequest(Request $request): Request
    {
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

    // Aktualisiert ein Register basierend auf den Daten der Anfrage und der Register-ID
    public function update(Request $request, int $id): JsonResponse
    {
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Finde das erste Register mit der gegebenen ID und lade seine Notizen
            $register = Register::with(['notes'])
                ->where('id', $id)->first();
            if ($register != null) {
                // Parse die Anfrage und aktualisiere das Register
                $request = $this->parseRequest($request);
                $register->update($request->all());
                // Speichere das Register
                $register->save();
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Lade das aktualisierte Register mit seinen Notizen
            $register1 = Register::with(['notes'])
                ->where('id', $id)->first();
            return response()->json($register1, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);
        }
    }

    // Löscht ein Register anhand seiner ID
    public function delete(int $id): JsonResponse
    {
        // Finde das erste Register mit der gegebenen ID
        $register = Register::where('id', $id)->first();
        if ($register != null) {
            // Lösche das Register
            $register->delete();
            return response()->json('register (' . $id . ') successfully deleted', 200);
        }
        else {
            // Wenn kein Register gefunden wurde, gebe einen Fehler zurück
            return response()->json('register could not be deleted - it does not exist', 422);
        }
    }

    // Weist Notizen einem Register zu
    public function assignNotes(Request $request, $registerId) {
        // Finde das Register anhand seiner ID
        $register = Register::find($registerId);
        if (!$register) {
            // Wenn das Register nicht gefunden wird, gebe eine Fehlermeldung zurück
            return response()->json(['message' => 'Register not found'], 404);
        }

        // Weise die Notizen, die in der Anfrage gesendet wurden, dem Register zu
        $noteIds = $request->get('noteIds', []);
        $register->notes()->sync($noteIds);

        // Lade das Register neu, um die aktualisierten Notizen zu zeigen
        $register = Register::with('notes')->find($registerId);

        // Gibt das aktualisierte Register zurück
        return response()->json($register);
    }
  /*  public function assignNotes(Request $request, $registerId) {
        // Finde das Register anhand seiner ID
        $register = Register::find($registerId);
        if (!$register) {
            // Wenn das Register nicht gefunden wird, gebe eine Fehlermeldung zurück
            return response()->json(['message' => 'Register not found'], 404);
        }

        // Erwarte, dass 'note_id' im Request übergeben wird
        $noteId = $request->input('note_id');
        if (!$noteId) {
            // Wenn 'note_id' nicht im Request ist, gebe eine Fehlermeldung zurück
            return response()->json(['message' => 'Note ID is required'], 400);
        }

        // Finde die Notiz anhand ihrer ID
        $note = Note::find($noteId);
        if (!$note) {
            // Wenn die Notiz nicht gefunden wird, gebe eine Fehlermeldung zurück
            return response()->json(['message' => 'Note not found'], 404);
        }

        // Weise die Notiz dem Register zu
        // Nutze Eloquent's attach() Methode, wenn Sie eine Many-to-Many-Beziehung haben
        $register->notes()->attach($noteId);

        // Alternativ: Nutze sync() ohne Duplikate, wenn es sich um wiederholte Zuweisungen handelt
        // $register->notes()->syncWithoutDetaching([$noteId]);

        // Gebe eine Erfolgsmeldung zurück
        return response()->json(['message' => 'Note assigned to register successfully'], 200);
    }*/
}
