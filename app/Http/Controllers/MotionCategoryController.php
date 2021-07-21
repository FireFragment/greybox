<?php


namespace App\Http\Controllers;

use App\Models\MotionCategory,
    App\Translation;
use Illuminate\Http\Request;

class MotionCategoryController extends Controller
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
        $motionCategories = MotionCategory::all();
        foreach ($motionCategories as $motionCategory)
        {
            $motionCategory->name = $motionCategory->translation()->first();
            $motionCategory->motionsCount = $motionCategory->motions()->count();
        }
        return response()->json($motionCategories);
    }

    public function showOne($id)
    {
        $motionCategory = MotionCategory::findOrFail($id);

        $motionCategory->name = $motionCategory->translation()->first();
        $motionCategory->motions = $motionCategory->getMotionsWithTexts();

        return response()->json($motionCategory);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name_cs' => 'required',
            'name_en' => 'required'
        ]);

        try {
            $translation = Translation::create([
                'cs' => $request->input('name_cs'),
                'en' => $request->input('name_en')
            ]);

            $motionCategory = MotionCategory::create([
               'name' => $translation->id
            ]);

            $motionCategory->name = $translation;
            return response()->json($motionCategory, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function update($id, Request $request)
    {
        try {
            $motionCategory = MotionCategory::findOrFail($id);

            if ($request->has('name_cs')) {
                $motionCategory->translation()->update([
                    'cs' => $request->input('name_cs')
                ]);
            }
            if ($request->has('name_en')) {
                $motionCategory->translation()->update([
                    'en' => $request->input('name_en')
                ]);
            }

            $motionCategory->name = $motionCategory->translation()->first();
            return response()->json($motionCategory, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $motionCategory = MotionCategory::findOrFail($id);
            $motionCategory->delete();
            $motionCategory->translation()->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}