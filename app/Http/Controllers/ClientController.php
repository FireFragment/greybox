<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;

use Fakturoid\Client as FakturoidClient;
use Fakturoid\Exception as FakturoidException;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Client::all());
    }

    public function showOne($id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'user' => 'required'
        ]);

        $data = $request->all();

        try {
            $fakturoidClient = new FakturoidClient(getenv('FAKTUROID_SLUG'), getenv('FAKTUROID_EMAIL'), getenv('FAKTUROID_API_KEY'), getenv('FAKTUROID_USER_AGENT'));

            $response = $fakturoidClient->createSubject($data);
            $data['fakturoid_id'] = $response->getBody()->id;

            try {
                $client = Client::create($data);
                return response()->json($client, 201);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } catch (FakturoidException $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
    
    public function update($id, Request $request)
    {
        try {
            $client = Client::findOrFail($id);

            if ($request->has('name')) $this->updateColumn($client, 'name', $request->input('name'));

            return response()->json($client, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Client::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}