<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cloth;

use App\Http\Requests\ClothStoreRequest;

class ClothController extends Controller
{

    public function store(ClothStoreRequest $request)
    {
        Cloth::create($request->validated());
    }

    public function update(ClothStoreRequest $request, Cloth $cloth)
    {
        $cloth->update($request->validated());
    }

    public function destroy(Cloth $cloth)
    {
        $cloth->delete();

        return response()->json([
            'title' => 'Delete Successful',
            'message' => 'A piece of clothing has been deleted.',
            'write' => true,
        ], 200);
    }

    public function show(Cloth $cloth)
    {
        return response()->json([
            'title' => 'show Successful',
            'message' => '',
            'write' => false,
            'data' => $cloth
        ], 200);
    }
}
