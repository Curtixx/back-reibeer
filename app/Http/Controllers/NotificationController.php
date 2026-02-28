<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        $cacheKey = "notifications_page_{$page}";

        $notifications = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return Notification::latest()->paginate(10);
        });

        return response()->json($notifications);
    }

    public function destroy(Request $request, Notification $notification)
    {
        $notification->delete();

        // Clear the cache for the first few pages simply, or let the cache expire
        // Usually, a more robust invalidation is needed, but we clear at least page 1
        Cache::forget("notifications_page_1");
        Cache::forget("notifications_page_" . $request->input('page', 1));

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
