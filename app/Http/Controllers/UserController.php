<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get($id)
    {
        return User::query()->findOrFail($id);
    }

    public function changeLink(Request $request)
    {
        $validatorErrors = Validator::make($request->all(), [
            'link' => 'required|string|unique:users',
        ]);

        if ( $validatorErrors->fails() )
        {
            return response()->json([
                'errors' => $validatorErrors->errors(),
            ]);
        }

        $user = User::query()->findOrFail(auth()->user()->id);
        $user->short_link = $request->post('link');
        $user->save();

        return response()->json([
            'message' => 'Successfully changed link!',
            'code' => 200,
        ]);
    }
}
