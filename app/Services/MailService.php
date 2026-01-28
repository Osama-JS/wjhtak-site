<?php

namespace App\Services;

use App\Models\User;
use App\Mail\otpMail;
use App\Mail\WelcomeMail;
use App\Mail\PasswordResetMail;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send Verification OTP to user.
     */
    public function sendVerificationOtp(User $user, int $otp): void
    {
        try {
            Mail::to($user->email)->send(new otpMail($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send verification OTP to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Send Password Reset OTP to user.
     */
    public function sendPasswordResetOtp(User $user, int $otp): void
    {
        try {
            Mail::to($user->email)->send(new PasswordResetMail($otp));
        } catch (\Exception $e) {
            Log::error("Failed to send password reset OTP to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Send Welcome Email to user.
     */
    public function sendWelcomeEmail(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeMail($user->name));
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Send custom announcement to user.
     */
    public function sendAnnouncement(User $user, string $title, string $content, string $buttonUrl = null, string $buttonText = null): void
    {
        try {
            Mail::to($user->email)->send(new AnnouncementMail($title, $content, $buttonUrl, $buttonText));
        } catch (\Exception $e) {
            Log::error("Failed to send announcement to {$user->email}: " . $e->getMessage());
        }
    }
}
