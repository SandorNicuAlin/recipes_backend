<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function getAllForUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('id', $request->user()->id)->first();
        $groups = $user->groups;
        foreach ($groups as $group) {
            $groupModel = Group::where('id', $group['id'])->first();
            $group['members'] = $groupModel->users;
        }
        return response()->json(['groups' => $groups], 200);
    }

    public function show(Request $request)
    {
        return response()->json(['groups' => Group::all()] ,200);
    }
}
