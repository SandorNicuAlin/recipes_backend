<?php

namespace App\Http\Controllers;

use App\Models\GroupUser;
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
use phpDocumentor\Reflection\Types\String_;

class UserController
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
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

    public function login(Request $request): \Illuminate\Http\JsonResponse
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

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->where('tokenable_id', $user->id)->delete();
        } catch (\Exception $e) {
            return response()->json(['success' => false], 400);
        }
        return response()->json(['success' => true], 200);
    }

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $selector = $request->get('selector');
        $value = $request->get('value');

        // check if the user didn't change anything, if he did we perform the changes in db
        if($value !== $request->user()[$selector]){
            // input validation
            $rule = '';
            switch ($selector) {
                case 'username':
                    $rule = "unique:users|required|min:3";
                    break;
                case 'email':
                    $rule = "unique:users|required|email";
                    break;
                case 'phone':
                    $rule = "required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10";
                    break;
            }

            $validator = Validator::make([$selector => $value], [$selector => $rule]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->messages()], 400);
            }

            //edit user
            UserRepository::editUser($request->user()->id, $selector, $value);
        }
        return response()->json(['success' => true], 200);
    }

    public function getAllThatDontBelongToGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        $members_of_this_group = GroupUser::where('group_id', $request->get('group_id'))->pluck('user_id')->toArray();
        return response()->json(['non_members_of_group' => User::all()->whereNotIn('id', $members_of_this_group)], 200);
    }
}
