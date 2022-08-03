<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\FormValidation;
use App\Repositories\UserRepository;

class UserController
{
    public function register(Request $request)
    {
        // input validation
        $validator = FormValidation::validate(
            $request,
            [
                "username" => "unique:users|required|min:3",
                "email" => "unique:users|required|email",
                "phone" => "required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10",
                "password" => "required|min:4",
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        // create user
        UserRepository::createUser(
            $request->get('username'),
            $request->get('email'),
            $request->get('phone'),
            $request->get('password'),
        );

        return response()->json(['success' => true], 200);
    }

    public function login(Request $request)
    {
        // input validation
        $validator = FormValidation::validate(
            $request,
            [
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        // check credentials
        $user = User::where('email', $request->get('email'))->first();

        if (UserRepository::checkCredentials($request, $user)) {
            return response()->json(['success' => false, 'error' => 'The provided credentials are incorrect.'], 401);
        }

        return response()->json(['success' => true, 'token' => $user->createToken($request->get('device_name'))->plainTextToken], 200);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->where('tokenable_id', $user->id)->delete();
        } catch (\Exception $e) {
            return response()->json(['success' => false], 400);
        }
        return response()->json(['success' => true], 200);
    }
}
