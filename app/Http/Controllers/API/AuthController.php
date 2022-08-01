<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function index()
    {
        $users = User::all();
        return $this->sendResponse($users, 'Display all users data');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return $this->sendError('Validation Error', $validator-errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['username'] = $user->name;
        return $this->sendResponse($success, 'User registered successfully!');
    }

    public function login(Request $request)
    {
        if(Auth::attempt(['name' => $request->name, 'password' => $request->password]))
        {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
            $success['username'] = $user->name;
            return $this->sendResponse($success, 'User login successfully!');
        }

        return $this->sendError('Unauthorised', ['error' => 'Unauthorised']);
    }
}
