<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Company;
use App\Models\Country;
use App\Models\City;
use App\Models\TripCategory;
use App\Models\TripImage;
use App\Models\TripItinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AgentTripController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Trip::where('company_id', $user->company_id);

        // Stats (unfiltered)
        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('active', true)->count(),
            'inactive' => (clone $query)->where('active', false)->count(),
            'expired' => (clone $query)->where('expiry_date', '<', now()->toDateString())->count(),
        ];

        // Apply Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('country_id')) {
            $query->where('to_country_id', $request->country_id);
        }
        if ($request->filled('city_id')) {
            $query->where('to_city_id', $request->city_id);
        }
        if ($request->filled('status')) {
            $status = $request->status === 'active' ? true : false;
            $query->where('active', $status);
        }

        $trips = $query->with(['fromCountry', 'toCountry', 'company'])
            ->latest()
            ->paginate(10);

        $countries = Country::active()->get();
        $cities = City::active()->get();

        return view('frontend.agent.trips.index', compact('trips', 'stats', 'countries', 'cities'));
    }


    public function create()
    {
        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = TripCategory::all();

        return view('frontend.agent.trips.create', compact('countries', 'cities', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'to_city_id'            => 'required|exists:cities,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date|after_or_equal:today',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'tickets'               => 'nullable|string',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'nullable|numeric|min:0',
            'is_public'             => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
        ]);

        $data['company_id'] = $user->company_id;
        $data['user_id'] = $user->id;

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['active']      = $request->has('active') ? $request->boolean('active') : true;

        // Default Admin fields for Agents
        $data['is_featured'] = false;
        $data['is_ad']       = false;

        $trip = Trip::create($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        }

        return redirect()->route('agent.trips.index')->with('success', __('Trip created successfully'));
    }

    public function edit(Trip $trip)
    {
        $this->authorizeAgent($trip);

        $countries = Country::active()->get();
        $cities = City::active()->get();
        $categories = TripCategory::all();

        return view('frontend.agent.trips.edit', compact('trip', 'countries', 'cities', 'categories'));
    }

    public function update(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);

        $data = $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'from_country_id'       => 'required|exists:countries,id',
            'from_city_id'          => 'required|exists:cities,id',
            'to_country_id'         => 'required|exists:countries,id',
            'to_city_id'            => 'required|exists:cities,id',
            'duration'              => 'nullable|string|max:100',
            'price'                 => 'required|numeric|min:0',
            'price_before_discount' => 'nullable|numeric|min:0',
            'expiry_date'           => 'nullable|date',
            'personnel_capacity'    => 'nullable|integer|min:1',
            'tickets'               => 'nullable|string',
            'base_capacity'         => 'nullable|integer|min:0',
            'extra_passenger_price' => 'nullable|numeric|min:0',
            'is_public'             => 'nullable|boolean',
            'active'                => 'nullable|boolean',
            'category_ids'          => 'nullable|array',
            'category_ids.*'        => 'exists:trip_categories,id',
        ]);

        // Checkbox handling
        $data['is_public']   = $request->boolean('is_public');
        $data['active']      = $request->boolean('active');

        $trip->update($data);

        if ($request->has('category_ids')) {
            $trip->categories()->sync($request->category_ids);
        }

        return redirect()->route('agent.trips.index')->with('success', __('Trip updated successfully'));
    }

    public function destroy(Trip $trip)
    {
        $this->authorizeAgent($trip);

        // Deletion Guard: Check for bookings
        if ($trip->bookings()->exists()) {
            return redirect()->route('agent.trips.index')->with('error', __('Cannot delete trip because it has existing bookings.'));
        }

        $trip->delete();
        return redirect()->route('agent.trips.index')->with('success', __('Trip deleted successfully'));
    }

    public function show(Trip $trip)
    {
        $this->authorizeAgent($trip);
        $trip->load(['images', 'itineraries' => function($q) {
            $q->orderBy('sort_order');
        }, 'bookings.user', 'fromCountry', 'toCountry', 'fromCity', 'toCity', 'company']);

        return view('frontend.agent.trips.show', compact('trip'));
    }

    // ─── Image Management ────────────────────────────────────────

    public function imageStore(Request $request, $trip_id)
    {
        $trip = Trip::findOrFail($trip_id);
        $this->authorizeAgent($trip);

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $trip_id) {
                $file = $request->file('file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('trips/' . $trip_id, $fileName, 'public');

                $newImage = TripImage::create([
                    'trip_id'    => $trip_id,
                    'image_path' => $path,
                ]);

                return response()->json([
                    'success' => true,
                    'id'      => $newImage->id,
                    'url'     => asset('storage/' . $path),
                    'message' => __('Image uploaded successfully'),
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error("Agent image upload failed for trip {$trip_id}: " . $e->getMessage());
            return response()->json(['error' => __('An error occurred during processing')], 500);
        }
    }

    public function imageDestroy(TripImage $image)
    {
        $this->authorizeAgent($image->trip);
        try {
            return DB::transaction(function () use ($image) {
                $path = $image->image_path;
                $image->delete();
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                return response()->json(['success' => true, 'message' => __('Image deleted successfully')]);
            });
        } catch (\Exception $e) {
            Log::error("Agent image delete failed ID {$image->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Sorry, an error occurred while trying to delete.')], 500);
        }
    }

    public function getImages($trip_id)
    {
        $trip = Trip::findOrFail($trip_id);
        $this->authorizeAgent($trip);

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

    // ─── Itinerary Management ─────────────────────────────────────

    public function storeItinerary(Request $request, Trip $trip)
    {
        $this->authorizeAgent($trip);
        $request->validate([
            'day_number'  => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
        ]);
        $lastOrder = $trip->itineraries()->max('sort_order') ?? 0;
        $trip->itineraries()->create(array_merge($request->all(), ['sort_order' => $lastOrder + 1]));
        return redirect()->back()->with('success', __('Itinerary added successfully'));
    }

    public function updateItinerary(Request $request, TripItinerary $itinerary)
    {
        $this->authorizeAgent($itinerary->trip);
        $request->validate([
            'day_number'  => 'required|integer',
            'title'       => 'required|string',
            'description' => 'nullable|string',
        ]);
        $itinerary->update($request->all());
        return response()->json(['success' => true, 'message' => __('Itinerary updated successfully')]);
    }

    public function destroyItinerary(TripItinerary $itinerary)
    {
        $this->authorizeAgent($itinerary->trip);
        $itinerary->delete();
        return redirect()->back()->with('success', __('Itinerary deleted successfully'));
    }

    public function reorderItinerary(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'exists:trip_itineraries,id',
        ]);
        foreach ($request->order as $index => $id) {
            TripItinerary::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true, 'message' => __('Itinerary reordered successfully')]);
    }

    // ─────────────────────────────────────────────────────────────

    protected function authorizeAgent(Trip $trip)
    {
        if ($trip->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
