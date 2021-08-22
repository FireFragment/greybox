<?php


namespace App\Http\Controllers;

use App\Models\Debate;
use App\User;
use Illuminate\Http\Request;

class DebateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'create',
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        $debate = Debate::all();
        return response()->json($debate);
    }

    public function showOne($id)
    {
        $debate = Debate::findOrFail($id);

        if (!empty($debateEvent = $debate->event()->first()))
        {
            $debate->event = $debateEvent;
            $debate->event->name = $debateEvent->nameTranslation()->first();
        }
        $debate->motion = $debate->motion()->first();
        $debate->motion->text = $debate->motion->textTranslation()->first();

        return response()->json($debate);
    }

    public function showDebatesForUser($id)
    {
        $user = User::findOrFail($id);
        $person = $user->person()->first();
        if (null !== $person)
        {
            $oldId = $person->getOldGreyboxId();
            if (is_numeric($oldId))
            {
                $gb = file_get_contents('https://debatovani.cz/greybox/?page=clovek&clovek_id='.$oldId);
                $debates = Debate::parseOldGreybox($gb);
                $debates = Debate::groupByMonth($debates);

                return response()->json($debates);
            }
        }
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'event' => 'integer',
            'motion' => 'required|integer',
            'date' => 'required|date',
            'place' => 'required|max:127',
            'affirmativeTeam' => 'required|integer',
            'negativeTeam' => 'required|integer',
            'affirmativeWinner' => 'prohibited',
            'unanimousDecision' => 'prohibited'
        ]);

        try {
            $debate = Debate::create($request->all());
            $debate->teams()->attach($request->input('affirmativeTeam'), ['side' => 'a']);
            $debate->teams()->attach($request->input('negativeTeam'), ['side' => 'n']);
            return response()->json($debate, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    // TODO: do
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'short_text_cs' => 'required_with:text_cs',
            'short_text_en' => 'required_with:text_en',
            'categories' => 'array',
            'categories.*' => 'integer'
        ]);

        try {
            $motion = Motion::findOrFail($id);

            if ($request->has('text_cs')) {
                $motion->textTranslation()->update([
                    'cs' => $request->input('text_cs')
                ]);
                $motion->shortTextTranslation()->update([
                    'cs' => $request->input('short_text_cs')
                ]);
            }
            if ($request->has('text_en')) {
                $motion->textTranslation()->update([
                    'en' => $request->input('text_en')
                ]);
                $motion->shortTextTranslation()->update([
                    'en' => $request->input('short_text_en')
                ]);
            }
            if ($request->has('note')) $this->updateColumn($motion, 'note', $request->input('note'));
            if ($request->has('categories')) $motion->categories()->sync($request->input('categories'));

            $motion->text = $motion->textTranslation()->first();
            $motion->short_text = $motion->shortTextTranslation()->first();
            $motion->categories = $motion->getCategoriesWithNames();
            return response()->json($motion, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $debate = Debate::findOrFail($id);
            event(new \App\Events\DebateDeletedEvent($debate));
            $debate->teams()->detach();
            $debate->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}