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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

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
        // Rate Limiting (Prevent spam)
        $key = 'register-attempt:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) { // 3 attempts per 10 mins
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => [__('محاولات كثيرة جداً. يرجى المحاولة بعد :seconds ثانية.', ['seconds' => $seconds])],
            ]);
        }
        RateLimiter::hit($key, 600); // 10 minutes

        // Check if user exists but is not verified
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && !$existingUser->email_verified_at) {
            $otp = rand(100000, 999999);
            $existingUser->otp_code = $otp;
            $existingUser->otp_expires_at = Carbon::now()->addMinutes(10);
            $existingUser->save();

            $sent = $this->mailService->sendVerificationOtp($existingUser, $otp);
            $request->session()->put('unverified_email', $existingUser->email);

            if (!$sent) {
                return redirect()->route('auth.verify-otp')->with('error', __('تعذر إرسال كود التحقق حالياً، يرجى المحاولة لاحقاً.'));
            }

            return redirect()->route('auth.verify-otp')->with('info', __('هذا البريد مسجل مسبقاً، تم إرسال كود تفعيل جديد.'));
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'      => ['required', 'string', 'max:20', 'unique:'.User::class, 'regex:/^(\+966|05)[0-9]{8}$/'],
            'password'   => [
                'required', 
                'confirmed', 
                Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
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
        $sent = $this->mailService->sendVerificationOtp($user, $otp);

        $request->session()->put('unverified_email', $user->email);

        if (!$sent) {
            return redirect()->route('auth.verify-otp')->with('error', __('تم إنشاء الحساب ولكن تعذر إرسال الكود، يرجى إعادة الإرسال من هذه الصفحة.'));
        }

        return redirect()->route('auth.verify-otp')->with('success', __('تم إنشاء الحساب بنجاح، يرجى إدخال كود التفعيل المرسل لبريدك.'));
    }
}
