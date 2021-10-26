<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cloth;

class ClothController extends Controller
{

    public function validateData(){ 
        
        return request()->validate([
            'image' => 'required',
            'description' => 'nullable',
            'category' => 'nullable|numeric',
            'buy_at' => 'nullable',
            'buy_date' => 'nullable',
            // IN USE
            'status' => 'required|numeric'

        ]);
    }

    public function store()
    {
        $data = $this->validateData();

        Cloth::create($data);
    }

    public function update(Cloth $cloth)
    {
        $data = $this->validateData();

        $cloth->update($data);
    }
}
