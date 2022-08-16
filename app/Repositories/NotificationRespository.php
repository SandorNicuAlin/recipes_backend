<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class NotificationRespository
{
    // create notifications
    public static function addGroupMember($user_id, $who_invited, $group)
    {
        DB::table('notifications')->insert([
            'user_id' => $user_id,
            'type' => 'group_invite',
            'text' => $who_invited . ' ' . 'invited you to join the group' . ' ' . $group,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // delete notification
    //..

    // mark notification as seen
}
