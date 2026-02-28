<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $page = $request->input('page', 1);

        $cacheKey = "notifications_user_{$user->id}_page_{$page}";

        $notifications = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            return Notification::where('user_id', $user->id)->latest()->paginate(10);
        });

        return response()->json($notifications);
    }

    public function destroy(Request $request, Notification $notification)
    {
        if ($request->user()->id !== $notification->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        // Clear the cache for the first few pages simply, or let the cache expire
        // Usually, a more robust invalidation is needed, but we clear at least page 1
        Cache::forget("notifications_user_{$notification->user_id}_page_1");
        Cache::forget("notifications_user_{$notification->user_id}_page_" . $request->input('page', 1));

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
