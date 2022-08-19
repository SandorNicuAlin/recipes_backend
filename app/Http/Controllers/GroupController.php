<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\GroupRepository;
use App\Repositories\NotificationRespository;
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

    public function addMembersNotification(Request $request): \Illuminate\Http\JsonResponse
    {

        $group_id = $request->get('group_id');
        // check if the group exist
        if(!Group::where('id', $group_id)->exists()) {
            return response()->json(['success' => false, 'error' => 'This group does not exist'], 400);
        }
        // check if the logged-in user is an administrator of this group
        if(GroupUser::where('group_id', $group_id)->where('user_id', $request->user()['id'])->first()->is_administrator === 0) {
            return response()->json(['success' => false, 'error' => 'You are not an administrator of this group'], 400);
        }

        // for every new member
        foreach ($request->get('new_members') as $member_id) {
            // check if the member exist as a user
            if(!User::where('id', $member_id)->exists()) {
                return response()->json(['success' => false, 'error' => 'One of the users does not exist'] , 400);
            }
            // check if this user is already a member of this group
            if(GroupUser::where('group_id', $group_id)->where('user_id', $member_id)->exists()) {
                return response()->json(['success' => false, 'error' => 'One of the users is already a member of this group'], 400);
            }
            // check if this new member does not have already an invitation to this group
            if(Notification::where('user_id', $member_id)->where('type', "group_invite [$group_id]")->exists())
            {
                continue;
            }

            // send notification to this user
            $user = User::where('id', $member_id)->first();
            NotificationRespository::addGroupMember(
                $member_id,
                $request->user()->username,
                Group::where('id', $request->get('group_id'))->first(),
            );
        }

        return response()->json(['success' => true], 200);
    }

    public function addMembers(Request $request): \Illuminate\Http\JsonResponse
    {
        $group_id = $request->get('group_id');
        $group = Group::where('id', $group_id)->first();
        $user = $request->user();

        // check if the group still exist
        if(!Group::where('id', $group_id)->exists()) {
            Notification::where('user_id', $user->id)->where('type', "group_invite [$group_id]")->delete();
            return response()->json(['success' => false, 'error' => 'Unfortunately this group does not exist anymore'], 400);
        }
        // check if the user is already a member of this group
        if(GroupUser::where('group_id', $group_id)->where('user_id', $user->id)->exists()) {
            Notification::where('user_id', $user->id)->where('type', "group_invite [$group_id]")->update(['seen' => 1]);
            return response()->json(['success' => false, 'error' => 'You are already a member of this group'], 400);
        }
        // check if the user was invited to the group
        if(!Notification::where('user_id', $user->id)->where('type', "group_invite [$group_id]")->exists()) {
            Notification::where('user_id', $user->id)->where('type', "group_invite [$group_id]")->delete();
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }

        $user->groups()->attach($group);
        Notification::where('user_id', $user->id)->where('type', "group_invite [$group_id]")->update(['seen' => 1]);

        return response()->json(['success' => true], 200);
    }

    public function makeAdministrator(Request $request): \Illuminate\Http\JsonResponse
    {
        // check if the group exist
        if(!Group::where('id', $request->get('group_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This group does not exist'], 400);
        }
        // check if the member exist as a user
        if(!User::where('id', $request->get('user_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This user does not exist'] , 400);
        }
        // check if this user is not a member of this group
        if(!GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->get('user_id'))->exists()) {
            return response()->json(['success' => false, 'error' => 'This user is not a member of this group'], 400);
        }
        // check if the logged-in user is an administrator of this group
        if(GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->user()['id'])->first()->is_administrator === 0) {
            return response()->json(['success' => false, 'error' => 'You are not an administrator of this group'], 400);
        }
        // check if this user is not already an administrator of this group
        if(GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->get('user_id'))->first()->is_administrator === 1) {
            return response()->json(['success' => false, 'error' => 'This user is already an administrator of this group'], 400);
        }

        GroupUser::where('group_id', $request->get('group_id'))->where('user_id', $request->get('user_id'))->update(['is_administrator' => 1]);

        return response()->json(['success' => true], 200);
    }
}
