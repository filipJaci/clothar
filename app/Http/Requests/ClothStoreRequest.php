<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClothStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'nullable',
            'category' => 'nullable|numeric',
            'buy_at' => 'nullable',
            'buy_date' => 'nullable|date',
            'status' => 'nullable|numeric'
        ];
    }
}
