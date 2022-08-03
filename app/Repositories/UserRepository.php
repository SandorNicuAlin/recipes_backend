<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public static function createUser($username, $email, $phone, $password): void {
        DB::table('users')->insert([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'password' => Hash::make($password),
        ]);
    }

    public static function checkCredentials($request, $user): bool {
        return !(!$user || !Hash::check($request->get('password'), $user->password));
    }
}
