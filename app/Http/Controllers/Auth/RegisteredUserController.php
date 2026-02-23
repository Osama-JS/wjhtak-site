<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\MailService;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if user exists but is not verified
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && !$existingUser->email_verified_at) {
            return back()->withInput()->with('unverified_email', $existingUser->email);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'      => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'user_type'  => User::TYPE_CUSTOMER,
            'status'     => 'pending',
            'otp_code'   => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        event(new Registered($user));

        // Send OTP via Email
        $this->mailService->sendVerificationOtp($user, $otp);

        $request->session()->put('unverified_email', $user->email);

        return redirect()->route('auth.verify-otp');
    }
}
