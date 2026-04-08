<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class HotelMarkupService
{
    /**
     * Calculate Sell Price based on Net Price and configured Markup.
     * 
     * @param float|int $netPrice The price from TBO
     * @param string|null $markupType 'percentage' or 'fixed'
     * @param float|int|null $markupValue
     * @return float
     */
    public function applyMarkup($netPrice, $markupType = null, $markupValue = null): float
    {
        $type  = $markupType  ?? Setting::get('hotel_markup_type', 'percentage');
        $value = $markupValue ?? (float) Setting::get('hotel_markup_value', 10);

        if ($type === 'percentage') {
            $sellPrice = $netPrice * (1 + ($value / 100));
        } else {
            $sellPrice = $netPrice + $value;
        }

        return round($sellPrice, 2);
    }

    /**
     * Map many hotel results and apply markup to each.
     */
    public function applyMarkupToHotels(array $hotels): array
    {
        return array_map(function ($hotel) {
            if (isset($hotel['LowestRate'])) {
                $hotel['LowestRate'] = $this->applyMarkup($hotel['LowestRate']);
            }
            return $hotel;
        }, $hotels);
    }

    /**
     * Map many room results and apply markup to each.
     */
    public function applyMarkupToRooms(array $rooms): array
    {
        return array_map(function ($room) {
            if (isset($room['TotalFare'])) {
                $room['TotalFare'] = $this->applyMarkup($room['TotalFare']);
            }
            // For TBO V5 structure
            if (isset($room['Price']['Total'])) {
                $room['Price']['Total'] = $this->applyMarkup($room['Price']['Total']);
            }
            return $room;
        }, $rooms);
    }
}
