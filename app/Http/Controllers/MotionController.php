<?php


namespace App\Http\Controllers;

use App\Models\Motion,
    App\Translation;
use Illuminate\Http\Request;

class MotionController extends Controller
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
        $motions = Motion::all();
        foreach ($motions as $motion)
        {
            $motion->text = $motion->textTranslation()->first();
            $motion->short_text = $motion->shortTextTranslation()->first();
            $motion->categoriesCount = $motion->categories()->count();
        }
        return response()->json($motions);
    }

    public function showOne($id)
    {
        $motion = Motion::find($id);

        if (null === $motion) {
            return response()->json(['message' => 'motionNotFound'], 404);
        }

        $motion->text = $motion->textTranslation()->first();
        $motion->short_text = $motion->shortTextTranslation()->first();
        $motion->categories = $motion->getCategoriesWithNames();

        return response()->json($motion);
    }

    /**
     * TODO: add creation with categories
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'text_cs' => 'required_without:text_en',
            'short_text_cs' => 'required_with:text_cs',
            'text_en' => 'required_without:text_cs',
            'short_text_en' => 'required_with:text_en'
        ]);

        try {
            $textTranslation = Translation::create([
                'cs' => $request->input('text_cs'),
                'en' => $request->input('text_en')
            ]);
            $shortTextTranslation = Translation::create([
                'cs' => $request->input('short_text_cs'),
                'en' => $request->input('short_text_en')
            ]);

            $motion = Motion::create([
               'text' => $textTranslation->id,
               'short_text' => $shortTextTranslation->id,
               'note' => $request->input('note')
            ]);

            $motion->text = $textTranslation;
            $motion->short_text = $shortTextTranslation;
            return response()->json($motion, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

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
            $motion = Motion::findOrFail($id);
            $motion->delete();
            $motion->textTranslation()->delete();
            $motion->shortTextTranslation()->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}