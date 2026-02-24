<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Display the OTP verification view for password reset.
     */
    public function show(Request $request): View
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-reset-otp', compact('email'));
    }

    /**
     * Handle the OTP verification request for password reset.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|numeric|digits:1',
            'email' => 'required|email|exists:users,email',
        ]);

        $otpCode = implode('', $request->otp);
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== $otpCode || $user->otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => __('كود التحقق غير صحيح أو منتهي الصلاحية.')]);
        }

        // Clear OTP
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Generate Laravel Password Reset Token
        $token = Password::createToken($user);

        // Redirect to standard reset password page with token
        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $user->email
        ]);
    }

    /**
     * Resend the OTP verification code.
     */
    public function resend(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request');
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $this->mailService->sendPasswordResetOtp($user, $otp);

        return back()->with('status', __('تم إعادة إرسال كود التحقق إلى بريدك الإلكتروني.'));
    }
}
