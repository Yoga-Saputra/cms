<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index',[
            'users' => User::all()
        ]);
    }

    public function makeAdmin(User $user)
    {
        $user->role = 'admin';
        $user->save();

        return response()->json([
            'status'=> true,
            'message' => 'Successfully update data'
        ], 200);
    }
}
