<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class DayStoreRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) { 
    throw new HttpResponseException(
      response()->json([
        'scenario' => 'cloth-day.store.failed.validation',
        'data' => $validator->errors()->all()
      // Response HTTP status code is 400 - Bad request.
      ], 400)
    );
  }

  /**
   * Determine if the User is authorized to make this request.
   *
   * @return bool
   */
  public function authorize(){
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(){
    return [
      'date' => 'required',
      'clothes' => 'required|array|exists:clothes,id',
    ];
  }
}
