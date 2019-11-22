<?php

namespace App\Http\Controllers;

use App\ShortLink;
use Exception;
use App\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get($id)
    {
        try {
            if (is_numeric($id)) {
                $user = User::query()->findOrFail($id);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'avatar' => $user->avatar,
                ];
            } else if (is_string($id)) {
                $link = ShortLink::query()->where('prefix', '=', $id)->firstOrFail();
                $user = User::query()->findOrFail($link->user_id);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'avatar' => $user->avatar,
                ];
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

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

    public function changeId(Request $request)
    {
        $validatorErrors = Validator::make($request->all(), [
            'prefix' => 'required|string|unique:short_links',
            'user_id' => 'required|integer',
        ]);
        if ($validatorErrors->fails()) {
            return response()->json([
                'errors' => $validatorErrors->errors(),
            ], 433);
        }
        (new ShortLink([
            'user_id' => $request->post('user_id'),
            'prefix' => $request->post('prefix'),
        ]))->save();

        return [
            'message' => 'Successfully changed id',
        ];
    }

    public function add(Request $request)
    {
        try {
            $validatorErrors = Validator::make($request->all(), [
                'friend_id' => 'required|integer'
            ]);

            if ($validatorErrors->fails()) {
                return response()->json([
                    'message' => $validatorErrors->errors(),
                ], 433);
            }

            $friend_id = $request->post('friend_id');

            $user = Friend::query()->where('user_id', $request->user()->id)->where('friend_id', $friend_id)->firstOrNew([
                'friend_id' => $friend_id,
                'user_id' => $request->user()->id,
            ])->save();

            return [
                'message' => 'Shipped',
            ];

        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
            ], 433);
        }
    }

    public function accept(Request $request)
    {
        $user = Friend::query()->where('is_friends', '<>', 1)->firstOrFail();
        if (!!$user->is_friends === false) {
            $user->is_friends = true;
            $user->save();

            return [
                'mesasge' => 'Accepted',
            ];
        }
    }
}
