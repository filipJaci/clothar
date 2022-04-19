<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendForgottenPasswordRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) { 
    throw new HttpResponseException(
      response()->json([
        'scenario' => 'forgotten.failed.email',
        'data' => $validator->errors()->all()
      // Response HTTP status code is 400 - Bad request.
      ], 400)
    );
  }

  /**
   * Determine if the user is authorized to make this request.
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
     'email' => 'required|exists:users,email'
    ];
  }
}
