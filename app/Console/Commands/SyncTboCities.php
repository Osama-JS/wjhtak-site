<?php

namespace App\Console\Commands;

use App\Models\TboCity;
use App\Services\TBOHotelService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTboCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tbo:sync-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the complete TBO city list into the local database for fast lookups.';

    protected $tboService;

    public function __construct(TBOHotelService $tboService)
    {
        parent::__construct();
        $this->tboService = $tboService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting TBO City Synchronization...');

        try {
            // Fetch everything from TBO
            $cities = $this->tboService->getCityList();

            if (empty($cities)) {
                $this->error('No cities returned from TBO API.');
                return 1;
            }

            $count = count($cities);
            $this->info("Fetched {$count} cities from API. Updating local database...");

            // Use bulk insert/upsert for performance
            $chunks = array_chunk($cities, 500);
            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($chunks as $chunk) {
                // Hardcoded demo map for Arabic names since TBO CityList is EN only
                $arabicMap = [
                    'Riyadh' => 'الرياض',
                    'Jeddah' => 'جدة',
                    'Dammam' => 'الدمام',
                    'Dubai' => 'دبي',
                    'Cairo' => 'القاهرة',
                    'Istanbul' => 'اسطنبول',
                    'Mecca' => 'مكة المكرمة',
                    'Medina' => 'المدينة المنورة',
                    'Saudi Arabia' => 'المملكة العربية السعودية',
                    'United Arab Emirates' => 'الإمارات العربية المتحدة',
                    'Egypt' => 'مصر',
                    'Turkey' => 'تركيا',
                ];

                $upsertData = array_map(function ($city) use ($arabicMap) {
                    $cityName = $city['CityName'] ?? $city['Name'] ?? null;
                    $countryName = $city['CountryName'] ?? null;

                    // Try to find Arabic name from local tables (heuristic)
                    $localCity = DB::table('cities')->where('title', $cityName)->first();
                    $localCountry = DB::table('countries')->where('name', $countryName)->first();

                    return [
                        'city_code'       => $city['CityCode'] ?? $city['Code'] ?? null,
                        'name'            => $cityName,
                        'name_ar'         => $arabicMap[$cityName] ?? ($localCity->title_ar ?? null),
                        'country_code'    => $city['CountryCode'] ?? null,
                        'country_name'    => $countryName,
                        'country_name_ar' => $arabicMap[$countryName] ?? ($localCountry->name_ar ?? null),
                        'updated_at'      => now(),
                        'created_at'      => now(),
                    ];
                }, $chunk);

                // Filter out entries with null CityCode or Name
                $upsertData = array_filter($upsertData, fn($c) => !empty($c['city_code']) && !empty($c['name']));

                if (!empty($upsertData)) {
                    TboCity::upsert($upsertData, ['city_code'], ['name', 'country_code', 'country_name', 'updated_at']);
                }

                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info('City Synchronization completed successfully.');

        } catch (\Exception $e) {
            $this->error('Synchronization Failed: ' . $e->getMessage());
            Log::error('TBO Sync Cities Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
