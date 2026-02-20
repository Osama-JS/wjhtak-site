<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => User::where('user_type', User::TYPE_CUSTOMER)->count(),
            'active' => User::where('user_type', User::TYPE_CUSTOMER)->where('status', 'active')->count(),
            'inactive' => User::where('user_type', User::TYPE_CUSTOMER)->where('status', 'inactive')->count(),
            'unverified' => User::where('user_type', User::TYPE_CUSTOMER)->whereNull('phone_verified_at')->count(),
        ];
        return view('admin.subscribers.index', compact('stats'));
    }

    /**
     * Get subscribers for DataTables.
     */
    public function getData(Request $request)
    {
        $subscribers = User::where('user_type', User::TYPE_CUSTOMER)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $subscribers->map(function($user) {
                $statusBadge = $user->status === 'active'
                    ? '<span class="badge badge-success">'.__('Active').'</span>'
                    : '<span class="badge badge-danger">'.__('Inactive').'</span>';

                $verifiedBadge = $user->phone_verified_at
                    ? '<span class="badge badge-light">'.__('Verified').'</span>'
                    : '<span class="badge badge-warning">'.__('Unverified').'</span>';

                return [
                    'id' => $user->id,
                    'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-lg me-2" width="35" alt="">',
                    'info' => '<div>
                                <strong>' . $user->full_name . '</strong><br>
                                <small class="text-muted">' . $user->email . '</small>
                            </div>',
                    'phone' => ($user->country_code ? $user->country_code . ' ' : '') . $user->phone,
                    'status' => $statusBadge,
                    'verified' => $verifiedBadge,
                    'actions' => '
                        <div class="d-flex">
                            <a href="' . route('admin.subscribers.profile', $user->id) . '" class="btn btn-info shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></a>
                            <button onclick="editSubscriber(' . $user->id . ')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="toggleSubscriberStatus(' . $user->id . ')" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fas fa-ban"></i></button>
                            <button onclick="deleteSubscriber(' . $user->id . ')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'user' => $user,
            'photo_url' => $user->profile_photo_url,
            'created_at' => $user->created_at->format('Y-m-d H:i')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:10',
            'password'     => 'required|min:8',
            'status'       => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['user_type'] = User::TYPE_CUSTOMER;

        User::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Subscriber created successfully')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'country_code' => 'nullable|string|max:10',
            'password' => 'nullable|min:8',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'country_code', 'status', 'country', 'city', 'address', 'gender', 'date_of_birth']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Subscriber updated successfully')
        ]);
    }

    /**
     * Toggle subscriber status.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => __('Subscriber status updated to ') . $newStatus,
            'status' => $newStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        try {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => __('Subscriber deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete subscriber')
            ], 500);
        }
    }

    /**
     * Show subscriber profile
     */
    public function profile($id)
    {
        $user = User::with(['bookings.trip'])->findOrFail($id);
        return view('admin.subscribers.profile', compact('user'));
    }
}
