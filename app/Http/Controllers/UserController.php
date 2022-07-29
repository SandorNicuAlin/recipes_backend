<?php

namespace App\Http\Controllers;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        error_log($username);
        error_log($email);
        error_log($phone);
        error_log($password);

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
}
