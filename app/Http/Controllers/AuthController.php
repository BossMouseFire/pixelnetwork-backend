<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    public function signup(Request $request)
    {
        $validatorErrors = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|string',
        ]);

        if ( $validatorErrors->fails() )
        {
            return response()->json($validatorErrors->errors(), 200);
        }

        $user = new User();
        $user->name = $request->post('name');
        $user->surname = $request->post('surname');
        $user->email = $request->post('email');
        $user->password = Hash::make($request->post('password'));
        $user->save();

        return response()->json([
            'message' => 'Successfully register!',
            'code' => 200,
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if ( !$token = auth()->attempt($credentials) )
        {
            return response()->json([
                'message' => 'Unauthorized!',
                'code' => 401,
            ], 401);
        }
        return $this->responseWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Successfully logged out!',
            'code' => 200,
        ], 200);
    }

    public function refresh()
    {
        return response()->json($this->responseWithToken(auth()->refresh()));
    }

    protected function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => auth()->factory()->getTTL() * 60,
        ], 200);
    }
}
