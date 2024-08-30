<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Termwind\Components\Raw;

class AuthController extends Controller
{
    public function register(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|email|unique:users,email',

            'password' => 'required'

        ]);

        if (!$validator->passes()) {

            return response()->json([
                'status' => false,
                'message' => 'Not Registered',
                'errors' => $validator->errors()
            ]);
        }

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Registered'
        ]);
    }


    public function login(Request $request)
    {

        $cred = $request->only('email', 'password');

        $validator = Validator::make($cred, [

            'email' => 'required',
            'password' => 'required'

        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status' => "false",
                'errors' => $validator->errors()
            ]);
        }


        if (Auth::attempt($cred)) {

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $token = $user->createToken('CurdLaravel')->plainTextToken;

            return response()->json([
                'message' => 'Logged In',
                'status' => true,
                'tokens' => $token
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Login failed: Invalid credentials'
            ], 401);
        }
    }

    public function home(){

        $id= Auth::user()->id;

        $singleuser = User::find($id);


        $user = User::orderBy('created_at', 'desc')->get();
    
    
        return response()->json([
            'status' => true,
            'singleuser' => $singleuser,
            'user' => $user,
        ]);
    }
}
