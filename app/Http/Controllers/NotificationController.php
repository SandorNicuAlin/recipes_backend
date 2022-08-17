<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getAllByUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user['id'])->latest()->get();

        return response()->json(['notifications' => $notifications], 200);
    }

    public function remove(Request $request): \Illuminate\Http\JsonResponse
    {
        $notification_id = $request->get('notification_id');
        // check if this notification correspond with this user
        if(Notification::where('id', $notification_id)->value('user_id') !== $request->user()['id'])
        {
            return response()->json(['success' => false, 'error' => 'Something went wrong'], 400);
        }


        Notification::where('id', $notification_id)->delete();

        return response()->json(['success' => true], 200);
    }
}
