<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
}
