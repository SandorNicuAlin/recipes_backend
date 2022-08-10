<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupUser;

class GroupRepository
{
    public static function fetchAllGroupsForASpecificUser(\App\Models\User $user) {
        // get all the groups for this user
        $groups = $user->groups;
        foreach ($groups as $group) {
            // get all the members for every group fetched
            $groupModel = Group::where('id', $group['id'])->first();
            $group['members'] = $groupModel->users;
            // set if this user is administrator for this group
            $group['is_administrator'] = GroupUser::where('group_id', $group['id'])->where('user_id', $user['id'])->first()->is_administrator;
            // get the administrator state for every member
            foreach ($group['members'] as $member) {
                $member['is_administrator'] = GroupUser::where('group_id', $group['id'])->where('user_id', $member['id'])->first()->is_administrator;
            }
        }
        return $groups;
    }

    public static function createGroup($name) {
        return Group::factory()->create(['name' => $name]);
    }
}
