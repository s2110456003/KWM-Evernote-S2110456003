<?php

namespace App\Http\Controllers;

use App\Models\CategoryTag;
use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\TodoEntry;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    // Funktion, um alle Notizen zusammen mit ihren Registern, Bildern, Benutzern und Tags zu laden
    public function index(): JsonResponse
    {
        // Lädt alle Notizen mit den zugehörigen Registern, Bildern, Benutzern und Tags
        $notes = Note::with(['registers', 'images', 'user', 'tags', 'todoEntries'])->get();
        // Gibt die Notizen als JSON-Antwort zurück
        return response()->json($notes, 200);
    }

    // Funktion, um eine einzelne Notiz anhand ihrer ID zu finden
    public function findById(int $id): JsonResponse
    {
        // Sucht die erste Notiz, die der ID entspricht, mit ihren Registern, Bildern, Benutzern und Tags
        $note = Note::where('id', $id)->with(['registers', 'images', 'user', 'tags', 'todoEntries'])->first();
        // Gibt die gefundene Notiz zurück oder null, wenn keine Notiz gefunden wurde
        return $note != null ? response()->json($note, 200) : response()->json(null, 200);
    }

    // Funktion, um zu überprüfen, ob eine Notiz mit einer bestimmten ID existiert
    public function checkId(int $id): JsonResponse
    {
        // Sucht die erste Notiz, die der ID entspricht
        $note = Note::where('id', $id)->first();
        // Gibt true zurück, wenn die Notiz gefunden wurde, sonst false
        return $note != null ? response()->json(true, 200) : response()->json(false, 200);
    }

    // Funktion, um Notizen anhand eines Suchbegriffs zu finden
    public function findBySearchTerm(string $searchTerm): JsonResponse
    {
        // Sucht Notizen, deren Titel oder Beschreibung den Suchbegriff enthält
        $notes = Note::with(['registers', 'images', 'user'])
            ->where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')->get();
        // Gibt die gefundenen Notizen zurück
        return response()->json($notes, 200);
    }

    // Funktion, um eine neue Notiz zu speichern
   /* public function save(Request $request): JsonResponse
    {
        // Parse und formatiere das Datum der Anfrage
        $request = $this->parseRequest($request);
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Erstelle eine neue Notiz mit den Daten aus der Anfrage
            $note = Note::create($request->all());

            // Wenn Bilder mitgesendet wurden, speichere diese Bilder zur Notiz
            if (isset($request['images']) && is_array($request['images'])) {
                foreach ($request['images'] as $img) {
                    $image = Image::firstOrNew(['url' => $img['url'], 'title' => $img['title']]);
                    $note->images()->save($image);
                }
            }

            // Wenn ein Register mitgesendet wurde, verknüpfe es mit der Notiz
            if(isset($request['register'])){
                $reg = $request['register'];
                $register = Register::firstOrNew(['title'=>$reg['title']]);
                $note->register()->save($register);
            }

            // Wenn Tags mitgesendet wurden, verknüpfe sie mit der Notiz
            if (isset($request['tags']) && is_array($request['tags'])) {
                foreach ($request['tags'] as $tagId) {
                    $tag = TodoEntry::find($tagId);
                    if ($tag) {
                        $tag->note_id = $note->id;
                        $tag->save();
                    }
                }
            }

            // Wenn ToDo-Einträge mitgesendet wurden, verknüpfe sie mit der Notiz
            if (isset($request['todoEntries']) && is_array($request['todoEntries'])) {
                foreach ($request['todoEntries'] as $todoId) {
                    $todo = TodoEntry::find($todoId);
                    if ($todo) {
                        $todo->note_id = $note->id;
                        $todo->save();
                    }
                }
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Gibt die neu erstellte Notiz zurück
            return response()->json($note, 201);
        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);
        }
    }
*/
    public function save(Request $request): JsonResponse
    {
        $request = $this->parseRequest($request);
        DB::beginTransaction();
        try {
            $note = Note::create($request->all());

            // Speichern von Bildern
            if (isset($request['images']) && is_array($request['images'])) {
                foreach ($request['images'] as $img) {
                    $image = Image::firstOrNew(['url' => $img['url'], 'title' => $img['title']]);
                    $note->images()->save($image);
                }
            }

            // Verknüpfen mit Register über Zwischentabelle
            if (isset($request['registers']) && is_array($request['registers'])) {
                $registerIds = array_map(function ($register) {
                    $reg = Register::firstOrCreate(['title' => $register['title']]);
                    return $reg->id;
                }, $request['registers']);

                $note->registers()->sync($registerIds);
            }
/*
            // Verknüpfen von Tags über Zwischentabelle
            if (isset($request['tags']) && is_array($request['tags'])) {
                // Verwenden Sie `pluck('id')` wenn Sie ein Array von Objekten erhalten, sonst verwenden Sie direkt $request['tags']
                $tagIds = array_map(function ($tag) {
                    $t = CategoryTag::firstOrCreate(['name' => $tag['name']]); // Erstellt den Tag, wenn er nicht existiert
                    return $t->id;
                }, $request['tags']);

                $note->tags()->sync($tagIds);
            }
*/
            // Verknüpfen von TodoEntries
            if (isset($request['todoEntries']) && is_array($request['todoEntries'])) {
                foreach ($request['todoEntries'] as $todoId) {
                    $todo = TodoEntry::find($todoId);
                    if ($todo) {
                        $todo->note_id = $note->id;
                        $todo->save();
                    }
                }
            }

            DB::commit();
            return response()->json($note, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);
        }
    }

    private function parseRequest(Request $request): Request
    {
        if ($request->has('created_at')) {
            $date = new DateTime($request->created_at);
            $request['created_at'] = $date->format('Y-m-d H:i:s');
        }
        return $request;
    }
/*
    // Funktion, um eine Anfrage zu parsen und das Datum zu formatieren
    private function parseRequest(Request $request): Request
    {
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }
*/
    // Funktion, um eine Notiz zu aktualisieren
    public function update(Request $request, int $id): JsonResponse
    {
        // Starte eine Datenbank-Transaktion
        DB::beginTransaction();
        try {
            // Finde die erste Notiz mit der gegebenen ID und lade deren Register, Bilder und Benutzer
            $note = Note::where('id', $id)->with(['registers', 'images', 'user', 'tags', 'todoEntries'])->first();
            if ($note != null) {
                // Parse die Anfrage und aktualisiere die Notiz
                $request = $this->parseRequest($request);
                $note->update($request->all());

                // Lösche vorhandene Bilder der Notiz und speichere die neuen Bilder
                $note->images()->delete();
                if (isset($request['images']) && is_array($request['images'])) {
                    foreach ($request['images'] as $img) {
                        $image = Image::firstOrNew(['url' => $img['url'], 'title' => $img['title']]);
                        $note->images()->save($image);
                    }
                }
                // Speichere die Notiz
                $note->save();
            }
            // Bestätige die Datenbank-Transaktion
            DB::commit();
            // Lade die aktualisierte Notiz mit ihren Registern, Bildern und Benutzern
            $note1 = Note::with(['registers', 'images', 'user', 'tags'])
                ->where('id', $id)->first();
            return response()->json($note1, 201);

        } catch (\Exception $e) {
            // Bei einem Fehler, mache die Transaktion rückgängig
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);
        }
    }

    // Funktion, um eine Notiz zu löschen
    public function delete(int $id):JsonResponse{
        // Finde die erste Notiz mit der gegebenen ID
        $note = Note::where('id', $id)->first();
        if($note != null){
            // Lösche die Notiz
            $note->delete();
            return response()->json('note (' . $id . ') successfully deleted', 200);
        }
        else{
            // Wenn keine Notiz gefunden wurde, gebe einen Fehler zurück
            return response()->json('note could not be deleted - it does not exist', 422);
        }
    }

    public function assignTag(Request $request, Note $note)
    {
        $validated = $request->validate([
            'tag_id' => 'required|exists:category_tags,id'
        ]);

        // Fügt das Tag zur Notiz hinzu
        $note->tags()->attach($validated['tag_id']);

        // Lade die Notiz neu mit allen zugehörigen Tags
        return response()->json($note->load('tags'));
    }

  /* public function assignTag(Request $request, $noteId) {
        // Finde das Notiz anhand seiner ID
        $note = Note::find($noteId);
        if (!$note) {
            // Wenn die Notiz nicht gefunden wird, gebe eine Fehlermeldung zurück
            return response()->json(['message' => 'Note not found'], 404);
        }

        // Weise den Tag, die in der Anfrage gesendet wurden, der Notiz zu
        $tag_id = $request->get('$tag_id', []);
        $note->tags()->sync($tag_id);

        // Lade das Register neu, um die aktualisierten Notizen zu zeigen
        $note = Note::with('tags')->find($noteId);

        // Gibt das aktualisierte Register zurück
        return response()->json($note);
    }
*/
}
