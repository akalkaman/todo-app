<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendSuccess()
    {
        return response()->json();
    }

    public function sendResponse($data = [], $status = 200)
    {
        return response()->json([
            'data' => $data
        ], $status);
    }

    public function sendError($error = '', $status = 400)
    {
        return response()->json([
            'error' => $error
        ], $status);
    }
}
