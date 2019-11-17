<?php

namespace App\Http\Controllers;

use Exception;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get($id)
    {
        try {
            $user = User::query()->findOrFail($id);
            return [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'avatar_link' => $user->avatar,
            ];
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    // TODO: Create auth and register via JWT
    public function store(Request $request)
    {
        try {
            $validatorErrors = Validator::make($request->all(), [
                'name' => 'required|string',
                'surname' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validatorErrors->fails()) {
                $errors = $validatorErrors->errors();
                return response()->json([
                    'error' => $errors,
                    'code' => 422,
                ], 422);
            }
            $user = new User();
            $user->name = $request->post('name');
            $user->surname = $request->post('surname');
            $user->email = $request->post('email');
            $user->password = bcrypt($request->post('password'));
            $user->avatar = $request->file('avatar')->store(storage_path());;
            $user->save();

            return response()->json([
                'code' => 200,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
