<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'number' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->number,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Message sent successfully!',
            'data' => $contact,
        ], 201);
    }

    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();

        $formatted = $messages->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'msg' => $m->message,
                'time' => $m->created_at->diffForHumans(),
                'read' => $m->is_read,
            ];
        });

        return response()->json(['messages' => $formatted]);
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllRead()
    {
        ContactMessage::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'All marked as read']);
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }

    public function destroyAll()
    {
        ContactMessage::query()->delete();

        return response()->json(['message' => 'All messages deleted']);
    }
}
