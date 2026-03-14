<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $notifications = Notification::latest()->paginate($perPage);

        return response()->json($notifications);
    }

    public function destroy(Request $request, Notification $notification)
    {
        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
