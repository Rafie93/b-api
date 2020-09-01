<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => __('validation.fails'),
                    'errors'  => $this->restructureValidationErrors($exception->errors()),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return parent::render($request, $exception);
    }
    private function restructureValidationErrors(array $errors)
    {
        $errorList = [];

        foreach ($errors as $attribute => $messages) {
            $errorList[] = [
                'attribute' => $attribute,
                'message'   => $messages[0],
            ];
        }

        return $errorList;
    }
}
