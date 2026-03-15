<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserNoteController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index()
    {
        $notes = UserNote::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $note = UserNote::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Note added successfully'),
            'note' => $note
        ]);
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, UserNote $userNote)
    {
        if ($userNote->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $userNote->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Note updated successfully'),
            'note' => $userNote
        ]);
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(UserNote $userNote)
    {
        if ($userNote->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $userNote->delete();

        return response()->json([
            'success' => true,
            'message' => __('Note deleted successfully'),
        ]);
    }
}
