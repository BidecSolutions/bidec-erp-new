<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Traits\PushNotification;

class NotificationController extends Controller
{

    use PushNotification;


    public function sendPushNotification(Request $request){

        $deviceToken = "eWfxeLoXQCy8wdGpQC-LlQ:APA91bHpx0si2itpycN8Y4vnkjZKoLgcP2gC98kfYuQkpoxBm0KjMG41kzEVaNKr9wgADFISgT0NfiAREpvaWGHOT-o22_m2ajhQpqcIeEZURLWyMT9Qb7g";
        $title = "Welcome Nawaz";
        $body = "This is Body of my notification";

        $data = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];

        $response = $this->sendNotification($deviceToken, $title,$body, $data);

        return response()->json([
            'success' => true,
            'response' => $response
        ]);
    }



    public function student_notifications(Request $request)
    {
        // Validate input data for filters
        $validated = $request->validate([
            'student_id' => 'nullable|integer|exists:students,id', // Validate student_id if provided
        ]);

        // Fetch notifications and decode the 'data' field
        $notificationList = Notification::query()
            ->when(isset($validated['student_id']), function ($query) use ($validated) {
                $query->where('student_id', $validated['student_id']);
            })
            ->get()
            ->map(function ($notification) {
                $notification->data = json_decode($notification->data); // Decode 'data' field
                return $notification;
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Notifications retrieved successfully',
            'data' => $notificationList,
        ]);
    }


}
