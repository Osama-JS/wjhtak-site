<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TripBooking;
use Illuminate\Support\Facades\Storage;

class TicketUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(TripBooking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('Your Trip Tickets are Ready!'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('The tickets for your booking (:trip) are now available and attached to this email.', ['trip' => $this->booking->trip->title]))
            ->action(__('View Booking Details'), route('customer.bookings.show', $this->booking->id))
            ->line(__('Thank you for booking with us!'));

        if ($this->booking->ticket_file_path && Storage::disk('public')->exists($this->booking->ticket_file_path)) {
            $mail->attach(Storage::disk('public')->path($this->booking->ticket_file_path));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Tickets Uploaded'),
            'message' => __('Your tickets for trip :trip have been uploaded.', ['trip' => $this->booking->trip->title]),
            'url' => route('customer.bookings.show', $this->booking->id),
            'type' => 'ticket_uploaded'
        ];
    }
}
