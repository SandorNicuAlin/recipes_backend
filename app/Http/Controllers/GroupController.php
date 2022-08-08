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
        return response()->json(['groups' => $user->groups], 200);

    }

    public function show(Request $request)
    {
        return response()->json(['groups' => Group::all()] ,200);
    }
}
