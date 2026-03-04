<?php

namespace App\Services;

use App\Models\TripBooking;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate invoice for a booking
     *
     * @param TripBooking $booking
     * @return string|false Path to the generated PDF
     */
    public function generateInvoice(TripBooking $booking)
    {
        try {
            $booking->load(['user', 'trip.toCountry', 'trip.toCity', 'passengers']);

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

            $html = view('invoices.trip_booking', compact('booking'))->render();
            $mpdf->WriteHTML($html);

            $fileName = 'invoice_' . $booking->id . '_' . time() . '.pdf';
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
