<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest{

  // In case of failed validation.
  protected function failedValidation(Validator $validator) { 
    throw new HttpResponseException(
      response()->json([
        'scenario' => 'login.failed.validation',
        'data' => $validator->errors()->all()
      // Response HTTP status code is 422 - invalid data.
      ], 422)
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
      'email' => 'string|required|email',
      'password' => [
        'string',
        'required',
        'min:8',
        'max:40',
        'regex:/[a-z]/',      // must contain at least one lowercase letter
        'regex:/[A-Z]/',      // must contain at least one uppercase letter
        'regex:/[0-9]/',      // must contain at least one digit
        'regex:/[@$!%*#?&]/', // must contain a special character
      ],
    ];
  }
}
