<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Display the cities management page.
     */
    public function index()
    {
        $column = app()->getLocale() === 'ar' ? 'name' : 'nicename';
        $countries = Country::active()->orderBy($column)->get();

        $stats = [
            'total' => City::count(),
            'active' => City::where('active', true)->count(),
            'inactive' => City::where('active', false)->count(),
            'countries_count' => Country::has('cities')->count(),
        ];

        return view('admin.cities.index', compact('countries', 'stats'));
    }

    /**
     * Get cities data for DataTable.
     */
    public function getData(Request $request)
    {
        $query = City::with('country');

        // Filter by country if specified
        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        $cities = $query->get();

        $data = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'title' => $city->title,
                'country' => $city->country ?
                    '<span class="d-flex align-items-center"><img src="' . $city->country->flag_url . '" width="20" height="20" class="rounded-circle me-2">' . $city->country->name . '</span>'
                    : '---',
                'status' => $city->active
                    ? '<span class="badge badge-success">' . __('Active') . '</span>'
                    : '<span class="badge badge-danger">' . __('Inactive') . '</span>',
                'actions' => $this->getActionButtons($city),
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Generate action buttons HTML.
     */
    private function getActionButtons($city): string
    {
        $editBtn = '<button class="btn btn-primary btn-sm me-1" onclick="editCity(' . $city->id . ')" title="' . __('Edit') . '"><i class="fas fa-edit"></i></button>';
        $toggleBtn = '<button class="btn btn-' . ($city->active ? 'warning' : 'success') . ' btn-sm me-1" onclick="toggleCityStatus(' . $city->id . ')" title="' . __('Toggle Status') . '"><i class="fas fa-' . ($city->active ? 'ban' : 'check') . '"></i></button>';
        $deleteBtn = '<button class="btn btn-danger btn-sm" onclick="deleteCity(' . $city->id . ')" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>';

        return '<div class="d-flex">' . $editBtn . $toggleBtn . $deleteBtn . '</div>';
    }

    /**
     * Store a newly created city.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['country_id', 'title']);
        $data['active'] = $request->boolean('active', true);

        $cities = City::create($data);
        // dd($cities);
        return response()->json([
            'success' => true,
            'message' => __('City added successfully'),
        ]);
    }

    /**
     * Display the specified city.
     */
    public function show(City $city)
    {
        $city->load('country');

        return response()->json([
            'success' => true,
            'city' => $city,
            'country_name' => $city->country ? $city->country->name : null,
        ]);
    }

    /**
     * Update the specified city.
     */
    public function update(Request $request, City $city)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['country_id', 'name_ar']);
        $data['active'] = $request->boolean('active', true);

        $city->update($data);

        return response()->json([
            'success' => true,
            'message' => __('City updated successfully'),
        ]);
    }

    /**
     * Toggle city status.
     */
    public function toggleStatus(City $city)
    {
        $city->update(['active' => !$city->active]);

        return response()->json([
            'success' => true,
            'message' => $city->active ? __('City activated') : __('City deactivated'),
        ]);
    }

    /**
     * Remove the specified city.
     */
    public function destroy(City $city)
    {
        $city->delete();

        return response()->json([
            'success' => true,
            'message' => __('City deleted successfully'),
        ]);
    }

    /**
     * Get cities by country for dropdown.
     */
    public function byCountry(Country $country)
    {
        $cities = $country->cities()->active()->orderBy('name_' . app()->getLocale())->get(['id', 'name_ar', 'name_en']);

        return response()->json($cities);
    }
}
