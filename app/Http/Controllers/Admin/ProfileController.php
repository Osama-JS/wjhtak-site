<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the admin profile page.
     */
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update the admin profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Profile updated successfully.'),
        ]);
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Password updated successfully.'),
        ]);
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        \Log::info('Profile photo upload attempt.', [
            'has_file' => $request->hasFile('profile_photo'),
            'file_name' => $request->hasFile('profile_photo') ? $request->file('profile_photo')->getClientOriginalName() : 'none',
        ]);

        try {
            $request->validate([
                'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Increased max size to 5MB
            ]);

            $user = auth()->user();

            if ($request->hasFile('profile_photo')) {
                // Delete old photo if it exists
                if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                    \Storage::disk('public')->delete($user->profile_photo);
                }

                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                $user->update(['profile_photo' => $path]);

                $photoUrl = url('storage/' . $path);
                // If APP_URL is likely wrong, provide a more relative-friendly fallback
                if (str_contains($photoUrl, 'localhost/my-trip')) {
                    $photoUrl = str_replace(url('/'), '', $photoUrl);
                    if (!str_starts_with($photoUrl, '/')) $photoUrl = '/' . $photoUrl;
                }

                \Log::info('Profile photo updated.', ['path' => $path, 'url' => $photoUrl]);

                return response()->json([
                    'success' => true,
                    'message' => __('Profile photo updated successfully.'),
                    'photo_url' => $photoUrl,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('No file uploaded.'),
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Profile photo upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to upload photo: ') . $e->getMessage(),
            ], 500);
        }
    }
}
