<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminNotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'read_at' => $notification->read_at,
                    'is_read' => !is_null($notification->read_at),
                    'title' => $data['title'] ?? 'تنبيه قسط',
                    'message' => $data['message'] ?? '',
                    'label' => $data['label'] ?? '',
                    'customer_name' => $data['customer_name'] ?? '',
                    'amount' => $data['amount'] ?? 0,
                    'due_date' => $data['due_date'] ?? '',
                    'url' => $data['url'] ?? route('customers.index'),
                    'created_at' => $notification->created_at,
                ];
            });

        $unreadCount = DB::table('notifications')->whereNull('read_at')->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        DB::table('notifications')
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        DB::table('notifications')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
