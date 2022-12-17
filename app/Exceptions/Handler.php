<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ReflectionClass;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $handlersPrefix = '\App\Exceptions\Handlers\\';

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

    public function render($request, Throwable $e)
    {
        /**
         * You can add any custom handler you want in the folder \App\Exceptions\Handlers\
         * It's just follow the name patter, the exception class name followed by 'Handler'.
         * e.g: ModelNotFoundException -> ModelNotFoundExceptionHandler
         */
        $handlerName = $this->handlersPrefix . (new ReflectionClass($e))->getShortName() . 'Handler';

        if (class_exists($handlerName)) return (new $handlerName())->response($e);

        return parent::render($request, $e);
    }
}
