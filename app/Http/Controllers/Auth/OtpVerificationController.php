<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpVerificationController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Display the OTP verification view.
     */
    public function show(Request $request)
    {
        $email = $request->session()->get('unverified_email');

        if (!$email) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Handle the OTP verification request.
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

        $user->email_verified_at = Carbon::now();
        $user->status = 'active';
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);

        $request->session()->forget('unverified_email');

        return redirect()->route('customer.dashboard')->with('success', __('تم تفعيل الحساب بنجاح.'));
    }

    /**
     * Resend the OTP verification code.
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Rate Limiting (Prevent resend spam)
        $key = 'otp-resend:' . $request->ip() . ':' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) { // 3 attempts per 5 mins
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => [__('محاولات كثيرة جداً. يرجى المحاولة بعد :seconds ثانية.', ['seconds' => $seconds])],
            ]);
        }
        RateLimiter::hit($key, 300); // 5 minutes

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('info', __('الحساب مفعل بالفعل، يرجى تسجيل الدخول.'));
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $sent = $this->mailService->sendVerificationOtp($user, $otp);

        $request->session()->put('unverified_email', $user->email);

        if (!$sent) {
            return back()->with('error', __('تعذر إرسال كود التحقق حالياً، يرجى المحاولة لاحقاً.'));
        }

        return back()->with('success', __('تم إعادة إرسال كود التحقق إلى بريدك الإلكتروني.'));
    }
}
