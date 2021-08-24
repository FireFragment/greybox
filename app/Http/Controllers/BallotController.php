<?php


namespace App\Http\Controllers;

use App\Models\Ballot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class BallotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'create'
        ]]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'debate' => 'integer',
            'adjudicator' => 'integer',
            'oldGreyboxId' => 'integer',
            'ballot' => 'file'
        ]);

        try {
            $path = $request->file('ballot')->store('ballots', 's3');

            $ballot = Ballot::create([
                'debate' => $request->input('debate'),
                'adjudicator' => $request->input('adjudicator'),
                'filename' => basename($path),
                'url' => Storage::disk('s3')->url($path),
                'old_greybox_id' => $request->input('oldGreyboxId')
            ]);
            return response()->json($ballot, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
}