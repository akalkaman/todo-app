<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
                'name' => ['required', 'string', 'max:50'],
            ]
        );

        User::query()->create($data);

        return $this->sendSuccess();
    }

    public function login(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email', 'exists:users'],
                'password' => ['required', 'string', 'min:6'],
            ]
        );

        if ($token = auth()->attempt($data)) {
            return $this->sendResponse(
                [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->guard()->factory()->getTTL() * 60
                ]
            );
        }

        return $this->sendError('Unauthorized', 401);
    }
}
