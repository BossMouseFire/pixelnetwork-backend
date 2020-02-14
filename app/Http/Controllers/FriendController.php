<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\User;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function send(Request $request)
    {
        $sender = User::query()->find(auth()->user()->id);
        $recipient = User::query()->find($request->get('recipient_id'));

        $sender->befriend($recipient);

        return response()->json([
            'message' => 'Sended!',
            'code' => 200,
        ]);
    }

    public function accept(Request $request)
    {
        $user = User::query()->find(auth()->user()->id);
        $sender = User::query()->find($request->get('sender_id'));

        $user->acceptFriendRequest($sender);

        return response()->json([
            'message' => 'Accepted!',
            'code' => 200,
        ]);
    }

    public function getAllFriendships()
    {
        $sender = User::query()->find(auth()->user()->id);

        return response()->json($sender->getFriends());
    }

    public function denyFriendRequest(Request $request)
    {
        $recipient = User::query()->findOrFail(auth()->user()->id);
        $sender = $request->post('sender_id');

        $recipient->denyFriendRequest($sender);

        return response()->json([
            'message' => 'Denied!',
            'code' => 200,
        ]);
    }

    public function unFriend(Request $request)
    {
        $user = auth()->user()->id;
        $recipient = $request->post('recipient_id');

        $user->unfriend($recipient);

        return response()->json([
            'message' => 'Unfriended!',
            'code' => 200,
        ]);
    }
}
