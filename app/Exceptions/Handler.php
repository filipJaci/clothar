<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
      //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
      'current_password',
      'password',
      'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(){

      // Failed login check.
      $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        dd($request);
        if ($request->is('api/*')) {
          return response()->json([
            // title
            'title' => 'Login check.',
            // message
            'message' => 'Login check failed.',
            // message should be displayed
            'write' => false,
            // additional data
            'data' => null
          ], 401);
        }
      });
    }

    protected function invalidJson($request, ValidationException $exception){
      
      return response()->json([
        'title' => 'Data validation error.',
        'message' => $exception->errors(),
        'write' => true,
        'data' => new \stdClass()
      ], $exception->status);
    }
}
