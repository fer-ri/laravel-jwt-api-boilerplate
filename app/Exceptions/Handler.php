<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
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
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception                $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // return parent::render($request, $e);

        $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;

        $headers = $this->isHttpException($e) ? $e->getHeaders() : [];

        if (! $message = $e->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $code = $e->getCode() ?: 0;

        $response = [
            'status_code' => $statusCode,
            'message' => $message,
            'code' => $code,
            'errors' => [],
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
            $response['status_code'] = $e->status;

            $statusCode = $e->status;
        }

        if (config('app.debug')) {
            $response['debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'exception' => get_class($e),
                // 'trace' => collect($e->getTrace())->map(function ($trace) {
                //     return Arr::except($trace, ['args']);
                // })->all(),
            ];
        }

        return new JsonResponse(
            $response,
            $statusCode,
            $headers
        );
    }
}
