<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegistrationRequest extends FormRequest{
  
  // In case of failed validation.
  protected function failedValidation(Validator $validator) { 
    throw new HttpResponseException(
      response()->json([
        'scenario' => 'registration.failed.validation',
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
      'name' => 'string|required|unique:users,name|alpha_dash|min:8|max:40',
      'email' => 'string|required|email|unique:users,email',
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
