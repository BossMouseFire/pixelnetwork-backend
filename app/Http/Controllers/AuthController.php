<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validateError = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'name' => 'required|string',
            'surname' => 'required|string'
        ]);

        if ($validateError->fails()) {
            return response()->json([
                'errors' => $validateError->errors(),
            ], 433);
        }

        (new User([
            'email' => $request->post('email'),
            'password' => bcrypt($request->post('password')),
            'name' => $request->post('name'),
            'surname' => $request->post('surname'),
        ]))->save();

        return response()->json([
            'message' => 'User successfully created',
            'code' => 200,
        ], 200);
    }

    public function login(Request $request)
    {
        $validateErrors = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validateErrors->fails()) {
            return response()->json([
                'errors' => $validateErrors->errors(),
            ], 433);
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->post('remember_me')) {
            $token->expires_at = Carbon::now()->addDay();
        } else {
            $token->expires_at = Carbon::now()->addHours(12);
        }
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
