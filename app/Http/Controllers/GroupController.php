<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Services\FormValidation;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function getAllForUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        $groups = GroupRepository::fetchAllGroupsForASpecificUser($user);
        return response()->json(['groups' => $groups], 200);
    }

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['groups' => Group::all()] ,200);
    }

    public function add(Request $request): \Illuminate\Http\JsonResponse
    {
        // input validation
        $validator = FormValidation::validate(
            $request,
            [
                'name' => 'unique:groups|required|min:2'
            ],
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }

        // create group
        $user = User::where('id', $request->user()->id)->first();
        $group = GroupRepository::createGroup($request->get('name'));

        // attach this user to the group as administrator
        $user->groups()->attach($group, ['is_administrator' => true]);

        return response()->json(['success' => true], 200);
    }
}
