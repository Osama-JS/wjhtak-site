<?php

namespace App\Services;

use App\Models\TripBooking;
use App\Models\HotelBooking;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate invoice for a booking
     *
     * @param mixed $booking TripBooking or HotelBooking
     * @return string|false Path to the generated PDF
     */
    public function generateInvoice($booking)
    {
        try {
            $isHotel = ($booking instanceof HotelBooking);

            if ($isHotel) {
                $booking->load(['user', 'guests']);
                $view = 'invoices.hotel_booking';
            } else {
                $booking->load(['user', 'trip.toCountry', 'trip.toCity', 'passengers']);
                $view = 'invoices.trip_booking';
            }

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'tempDir' => storage_path('framework/cache')
            ]);

            // Set RTL for Arabic support
            $mpdf->SetDirectionality('rtl');

            $html = view($view, compact('booking'))->render();
            $mpdf->WriteHTML($html);

            $prefix = $isHotel ? 'hotel_inv_' : 'invoice_';
            $fileName = $prefix . $booking->id . '_' . time() . '.pdf';
            $filePath = 'invoices/' . $fileName;

            // Ensure the directory exists
            if (!Storage::disk('public')->exists('invoices')) {
                Storage::disk('public')->makeDirectory('invoices');
            }

            Storage::disk('public')->put($filePath, $mpdf->Output('', 'S'));

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Invoice Generation Failed: ' . $e->getMessage());
            return false;
        }
    }
}
