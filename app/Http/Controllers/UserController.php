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

class UserController
{
    public function register(Request $request)
    {
        $rules = [
            "username" => "unique:users|required|min:3",
            "email" => "unique:users|required|email",
            "phone" => "required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10",
            "password" => "required|min:4",
        ];

        $input = $request->only('username', 'email', 'phone', 'password');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        $username = $request->get('username');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $password = $request->get('password');

        DB::table('users')->insert([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'password' => Hash::make($password),
        ]);

        return response()->json(['success' => true], 200);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ];

        $input = $request->only('email', 'password', 'device_name');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        $user = User::where('email', $request->get('email'))->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
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
