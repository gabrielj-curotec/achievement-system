<?php

namespace App\Exceptions\Handlers;

class ModelNotFoundExceptionHandler
{
    public function response()
    {
        return response()->json(['message' => 'Not found!'], 404);
    }
}
