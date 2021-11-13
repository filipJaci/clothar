<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DayStoreRequest extends FormRequest
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
            'date' => 'required|date',
            'cloth_id' => 'required|numeric|exists:clothes,id',
            'ocassion' => 'required|numeric'
        ];
    }
}
