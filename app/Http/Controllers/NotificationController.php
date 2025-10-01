<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications untuk user yang sedang login
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('user_id', Auth::user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get hanya notifikasi yang belum dibaca (untuk polling)
     */
    public function unread()
    {
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai sudah dibaca'
        ]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai sudah dibaca'
        ]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ]);
    }
}