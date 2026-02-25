<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Country;
use App\Models\City;
use App\Models\Company;
use App\Models\Trip;
use App\Models\TripImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\TripItinerary;

class TripsController extends Controller
{
    public function index()
    {
        $trips = Trip::all();
        $companies = Company::all();
        $countries = Country::all();
        $cities = City::all();
        $categories = \App\Models\TripCategory::all();

        $stats = [
            'total' => Trip::count(),
            'active' => Trip::active()->count(),
            'inactive' => Trip::where('active', false)->count(),
            'expired' => Trip::where('expiry_date', '<', now()->toDateString())->count(),
        ];

        return view('admin.trips.index', compact('companies', 'countries', 'trips', 'stats','cities', 'categories'));
    }

    public function itinerary(Trip $trip)
    {
        return view('admin.trips.itinerary', compact('trip'));
    }

    public function storeItinerary(Request $request, Trip $trip)
    {
        $request->validate([
            'day_number' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        // Set sort_order as the same as day_number initially,
        // or just after the last one
        $lastOrder = $trip->itineraries()->max('sort_order') ?? 0;
        $data['sort_order'] = $lastOrder + 1;

        $trip->itineraries()->create($data);

        return redirect()->back()->with('success', __('Itinerary added successfully'));
    }

    public function updateItinerary(Request $request, TripItinerary $itinerary)
    {
        $request->validate([
            'day_number' => 'required|integer',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $itinerary->update($request->all());

        return response()->json([
            'success' => true,
            'message' => __('Itinerary updated successfully')
        ]);
    }

    public function reorderItinerary(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:trip_itineraries,id'
        ]);

        foreach ($request->order as $index => $id) {
            TripItinerary::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Itinerary reordered successfully')
        ]);
    }

    public function destroyItinerary(TripItinerary $itinerary)
    {
        $itinerary->delete();
        return redirect()->back()->with('success', __('Itinerary deleted successfully'));
    }


    public function getData(Request $request)
    {
         $query = Trip::with(['company','fromCountry','toCountry']);

         if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->from_country_id) {
            $query->where('from_country_id', $request->from_country_id);
        }

        if ($request->to_country_id) {
            $query->where('to_country_id', $request->to_country_id);
        }

        if ($request->expiry_date) {
           $query->whereDate('expiry_date', '>=', $request->expiry_date);
        }

        $trips = $query->latest()->get();

        return response()->json([
            'data' => $trips->map(function ($trip) {
                $isExpired = $trip->expiry_date && $trip->expiry_date < now()->format('Y-m-d');

                $actionButtons = '
                        <a href="'.route('admin.trips.edit', $trip->id).'" class="btn btn-sm btn-primary" title="'.__('Edit').'">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="'.route('admin.trips.itinerary', $trip->id).'" class="btn btn-sm btn-info" title="'.__('Itinerary').'">
                            <i class="fas fa-list-ul"></i>
                        </a>
                        <button class="btn btn-sm btn-secondary" onclick="openImageUpload('.$trip->id.', \''.addslashes($trip->title).'\')" title="'.__('Upload Images').'">
                            <i class="fas fa-camera"></i>
                        </button>';

                if ($isExpired) {
                    $actionButtons .= '
                        <button class="btn btn-sm btn-success" onclick="renewTrip('.$trip->id.')" title="تجديد الرحلة">
                            <i class="fas fa-sync-alt"></i>
                        </button>';
                } else {
                    $actionButtons .= '
                        <button class="btn btn-sm btn-warning" onclick="toggleTripStatus('.$trip->id.')">
                            <i class="fas fa-ban"></i>
                        </button>';
                }

                $actionButtons .= '
                        <button class="btn btn-sm btn-danger" onclick="deleteTrip('.$trip->id.')" title="'.__('Delete').'">
                            <i class="fas fa-trash"></i>
                        </button>';

                return [
                    'title'    => $trip->title,
                    'company' => $trip->company
                          ?  '<span>'. $trip->company->name .'</span>' : '...',
                    'fromCountry' => $trip->fromCountry
                          ?  '<span>'. $trip->fromCountry->name .'</span>' : '...',
                    'toCountry' => $trip->toCountry
                          ?  '<span>'. $trip->toCountry->name .'</span>' : '...',
                    'price'    => $trip->price,
                    'expiry_date' => $trip->expiry_date,
                    'status'      => $isExpired
                                    ? '<span class="badge bg-dark">' . __('Expired') . '</span>'
                                    : ($trip->active ? '<span class="badge bg-success">' . __('Active') . '</span>' : '<span class="badge bg-danger">' . __('Inactive') . '</span>'),
                    'actions' => $actionButtons,
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::active()->get();
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = \App\Models\TripCategory::all();

        return view('admin.trips.create', compact('companies', 'countries', 'cities', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                 => 'required|string|max:255',
            'tickets'               => 'nullable|string',
            'description'           => 'required|string',
            'company_id'            => 'required|exists:companies,id',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date|after_or_equal:today',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'is_public'             => 'nullable|boolean',
            'is_featured'           => 'nullable|boolean',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'nullable|numeric|min:0',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
            'is_ad'                 => 'nullable|boolean',
            'active'                => 'nullable|boolean',
        ]);

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_ad']       = $request->boolean('is_ad');
        $data['active']      = $request->boolean('active');

        // Admin who created the trip
        $data['admin_id'] = auth()->id();

        // Auto-calculate profit
        if (!empty($data['price_before_discount'])) {
            $data['profit'] = $data['price'] - $data['price_before_discount'];
            $data['percentage_profit_margin'] = $data['price_before_discount'] > 0
                ? round(($data['profit'] / $data['price_before_discount']) * 100, 2)
                : 0;
        }

        $trip = Trip::create($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' =>  __('Trip created successfully'),
            ]);
        }

        return redirect()->route('admin.trips.index')->with('success', __('Trip created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        return response()->json([
            'success' => true,
            'Trip' => $trip->load('categories'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trip $trip)
    {
        $companies = Company::active()->get();
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = \App\Models\TripCategory::all();

        return view('admin.trips.edit', compact('trip', 'companies', 'countries', 'cities', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'title'                 => 'required|string|max:255',
            'tickets'               => 'nullable|string',
            'description'           => 'required|string',
            'company_id'            => 'required|exists:companies,id',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'nullable|numeric|min:0',
            'is_public'             => 'nullable|boolean',
            'is_featured'           => 'nullable|boolean',
            'is_ad'                 => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
        ]);

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_ad']       = $request->boolean('is_ad');
        $data['active']      = $request->boolean('active');

        // Recalculate profit
        if (!empty($data['price_before_discount'])) {
            $data['profit'] = $data['price'] - $data['price_before_discount'];

            $data['percentage_profit_margin'] =
                $data['price_before_discount'] > 0
                    ? round(($data['profit'] / $data['price_before_discount']) * 100, 2)
                    : 0;
        } else {
            $data['profit'] = 0;
            $data['percentage_profit_margin'] = 0;
        }

        $oldPrice = $trip->price;
        $trip->update($data);
        $newPrice = $trip->price;

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        } else {
            $trip->categories()->detach();
        }

        // Send Notification to Favoriting Users if Price Dropped
        if ($newPrice < $oldPrice) {
            $favoritingUsers = \App\Models\User::whereHas('favorites', function ($query) use ($trip) {
                $query->where('trip_id', $trip->id);
            })->get();

            if ($favoritingUsers->isNotEmpty()) {
                $notificationService = app(\App\Services\NotificationService::class);
                foreach ($favoritingUsers as $user) {
                    $notificationService->sendToUser(
                        $user,
                        \App\Models\Notification::TYPE_FAVORITE_TRIP_UPDATE,
                        __('Price Drop Alert! 🎉'),
                        __('Great news! The price for your favorite trip ":trip" has dropped to :price.', [
                            'trip' => $trip->title,
                            'price' => $newPrice
                        ]),
                        ['trip_id' => $trip->id],
                        true
                    );
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' =>  __('Trip updated successfully'),
            ]);
        }

        return redirect()->route('admin.trips.index')->with('success', __('Trip updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */

    public function toggleStatus(Trip $trip)
    {
        $trip->active = ! $trip->active;
        $trip->save();

        return response()->json([
            'success' => true,
            'message' => __('Trip status updated successfully'),
            'status'  => $trip->active ? 'Active' : 'Inactive'
        ]);
    }

    public function renew(Request $request, $id)
    {
        $request->validate([
            'expiry_date' => 'required|date|after:today',
        ]);

        $trip = Trip::findOrFail($id);
        $trip->update([
            'expiry_date' => $request->expiry_date,
            'active'      => true // إعادة تفعيلها تلقائياً عند التجديد
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Trip deleted successfully'),

        ]);
    }


    public function destroy(Trip $trip)
    {
       $trip->delete();

        return response()->json([
            'success' => true,
            'message' => __('Trip deleted successfully'),
        ]);
    }

    public function imagestore(Request $request, $trip_id) // نمرر الـ ID مباشرة
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $trip_id) {

                if (!$request->hasFile('file')) {
                    throw new \Exception('File not found');
                }

                $file = $request->file('file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // تخزين في مجلد خاص بكل رحلة
                $path = $file->storeAs('trips/' . $trip_id, $fileName, 'public');

                // حفظ السجل
                $newImage = TripImage::create([
                    'trip_id' => $trip_id,
                    'image_path' => $path,
                ]);

                return response()->json([
                    'success' => true,
                    'id' => $newImage->id, // نرجع ID السجل الجديد
                    'url' => asset('storage/' . $path),
                    'message' => __('Trip created successfully'),
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error("__('The trip photo upload failed'){$trip_id}: " . $e->getMessage());
            return response()->json(['error' => __('An error occurred during processing')], 500);
        }
    }

    public function imagedestroy(TripImage $image)
    {
        try {


            return DB::transaction(function () use ($image) {

                $path = $image->image_path;
                $image->delete();

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }

                return response()->json([
                    'success' => true,
                    'message' => __('Trip deleted successfully'),
                ]);
            });

        } catch (\Exception $e) {
            Log::error("__('Error while deleting the image') ID {$image->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Sorry, an error occurred while trying to delete.'),
            ], 500);
        }
    }

   public function getImages($trip_id)
    {

        $images = TripImage::where('trip_id', $trip_id)->get();

        $data = $images->map(function ($image) {
            $path = storage_path('app/public/' . $image->image_path);
            return [
                'id'   => $image->id,
                'name' => basename($image->image_path),
                'size' => file_exists($path) ? filesize($path) : 0,
                'url'  => asset('storage/' . $image->image_path),
            ];
        });

        return response()->json($data);
    }

}
