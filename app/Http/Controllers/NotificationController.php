<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $perPage = $request->get('per_page', 20);
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        $unreadCount = $user->notifications()->whereNull('read_at')->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($id);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification introuvable.',
                ], 404);
            }
            
            if (!$notification->read_at) {
                $notification->update(['read_at' => now()]);
            }
            
            $unreadCount = $user->notifications()->whereNull('read_at')->count();
            
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => Auth::id(),
                'exception' => get_class($e),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification comme lue.',
            ], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            $user->notifications()
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking all notifications as read: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception' => get_class($e),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de toutes les notifications comme lues.',
            ], 500);
        }
    }

    /**
     * Get recent notifications (for AJAX).
     */
    public function get(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 5);
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        $unreadCount = $user->notifications()->whereNull('read_at')->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($id);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification introuvable.',
                ], 404);
            }
            
            $notification->delete();
            
            $unreadCount = $user->notifications()->whereNull('read_at')->count();
            
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting notification: ' . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => Auth::id(),
                'exception' => get_class($e),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la notification.',
            ], 500);
        }
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll(Request $request)
    {
        try {
            $user = Auth::user();
            $user->notifications()->delete();
            
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting all notifications: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception' => get_class($e),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression des notifications.',
            ], 500);
        }
    }
}




