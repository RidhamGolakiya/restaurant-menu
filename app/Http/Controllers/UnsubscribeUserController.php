<?php

namespace App\Http\Controllers;

use App\Models\UnsubscribedUser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnsubscribeUserController extends Controller
{
    public function index()
    {
        return view('unsubscribe');
    }

    public function unsubscribe(Request $request)
    {
        $messages = [
            'email.unique' => 'You have already been unsubscribed.',
        ];
        try {
            $request->validate([
                'email' => 'required|email|unique:unsubscribed_users,email',
            ], $messages);
            UnsubscribedUser::create([
                'email' => $request->email
            ]);

            return response()->json(['status' => true]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
