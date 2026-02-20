<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    /**
     * Display the countries management page.
     */
    public function index()
    {
        $stats = [
            'total' => Country::count(),
            'active' => Country::where('active', true)->count(),
            'inactive' => Country::where('active', false)->count(),
            'with_cities' => Country::has('cities')->count(),
        ];
        return view('admin.countries.index', compact('stats'));
    }

    /**
     * Get countries data for DataTable.
     */
    // public function getData()
    // {
    //     try{
    //         $countries = Country::withCount('cities')->get();

    //         $data = $countries->map(function ($country) {
    //             return [
    //                 'id' => $country->id,
    //                 'flag' => '<img src="' . $country->flag_url . '" alt="' . $country->nicename . '" class="rounded-circle" width="40" height="40" style="object-fit: cover;">',
    //                 'name' => $country->name,
    //                 'nicename' => $country->nicename,
    //                 'numcode' => '<span class="badge badge-light">' . $country->numcode . '</span>',
    //                 'phonecode' => $country->phonecode ?? '---',
    //                 'cities_count' => '<span class="badge badge-primary">' . $country->cities_count . '</span>',
    //                 'status' => $country->active
    //                     ? '<span class="badge badge-success">' . __('Active') . '</span>'
    //                     : '<span class="badge badge-danger">' . __('Inactive') . '</span>',
    //                 'actions' => $this->getActionButtons($country),
    //             ];
    //         });

    //         return response()->json(['data' => $data]);
    //     }catch (\Exception $e) {
    //     return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function getData()
    {
        try {
            $countries = Country::withCount('cities')->get();

            $data = $countries->map(function ($country) {
                return [
                    'id' => $country->id,
                    'flag' => '<img src="' . $country->flag_url . '" alt="' . $country->nicename . '" class="rounded-circle" width="40" height="40" style="object-fit: cover;">',
                    'name' => $country->name,
                    'nicename' => $country->nicename,
                    'numcode' => '<span class="badge badge-light">' . $country->numcode . '</span>',
                    'phonecode' => $country->phonecode ?? '---',
                    'cities_count' => '<span class="badge badge-primary">' . $country->cities_count . '</span>',
                    'landmark' => '<img src="' . $country->landmark_image_url . '" alt="' . $country->nicename . '" class="rounded" width="50" height="35" style="object-fit: cover;">',
                    'status' => $country->active
                        ? '<span class="badge badge-success">' . __('Active') . '</span>'
                        : '<span class="badge badge-danger">' . __('Inactive') . '</span>',
                    'actions' => $this->getActionButtons($country),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate action buttons HTML.
     */
    private function getActionButtons($country): string
    {
        $editBtn = '<button class="btn btn-primary btn-sm me-1" onclick="editCountry(' . $country->id . ')" title="' . __('Edit') . '"><i class="fas fa-edit"></i></button>';
        $toggleBtn = '<button class="btn btn-' . ($country->active ? 'warning' : 'success') . ' btn-sm me-1" onclick="toggleCountryStatus(' . $country->id . ')" title="' . __('Toggle Status') . '"><i class="fas fa-' . ($country->active ? 'ban' : 'check') . '"></i></button>';
        $deleteBtn = '<button class="btn btn-danger btn-sm" onclick="deleteCountry(' . $country->id . ')" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>';

        return '<div class="d-flex">' . $editBtn . $toggleBtn . $deleteBtn . '</div>';
    }

    /**
     * Store a newly created country.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nicename' => 'required|string|max:255',
            'numcode' => 'required|string|max:10|unique:countries,numcode',
            'phonecode' => 'nullable|string|max:10',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'landmark_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'nicename', 'numcode', 'phonecode']);
        $data['active'] = $request->boolean('active', true);

        // Handle flag upload
        if ($request->hasFile('flag')) {
            $data['flag'] = $request->file('flag')->store('flags', 'public');
        }

        // Handle landmark image upload
        if ($request->hasFile('landmark_image')) {
            $data['landmark_image'] = $request->file('landmark_image')->store('landmarks', 'public');
        }

        $countrie = Country::create($data);

        // dd($countrie);

        return response()->json([
            'success' => true,
            'message' => __('Country added successfully'),
        ]);
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        return response()->json([
            'success' => true,
            'country' => $country,
            'flag_url' => $country->flag_url,
            'landmark_image_url' => $country->landmark_image_url,
        ]);
    }

    /**
     * Update the specified country.
     */
    public function update(Request $request, Country $country)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nicename' => 'required|string|max:255',
            'numcode' => 'required|string|max:10|unique:countries,numcode,' . $country->id,
            'phonecode' => 'nullable|string|max:10',
            'flag'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'landmark_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'nicename', 'numcode', 'phonecode']);
        $data['active'] = $request->boolean('active', true);

        // Handle flag upload
        if ($request->hasFile('flag')) {
            // Delete old flag
            if ($country->flag) {
                Storage::disk('public')->delete($country->flag);
            }
            $data['flag'] = $request->file('flag')->store('flags', 'public');
        }

        // Handle landmark image upload
        if ($request->hasFile('landmark_image')) {
            // Delete old landmark image
            if ($country->landmark_image) {
                Storage::disk('public')->delete($country->landmark_image);
            }
            $data['landmark_image'] = $request->file('landmark_image')->store('landmarks', 'public');
        }

        $country->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Country updated successfully'),
        ]);
    }

    /**
     * Toggle country status.
     */
    public function toggleStatus(Country $country)
    {
        $country->update(['active' => !$country->active]);

        return response()->json([
            'success' => true,
            'message' => $country->active ? __('Country activated') : __('Country deactivated'),
        ]);
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country)
    {
        // Delete flag if exists
        if ($country->flag) {
            Storage::disk('public')->delete($country->flag);
        }

        // Delete landmark image if exists
        if ($country->landmark_image) {
            Storage::disk('public')->delete($country->landmark_image);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'message' => __('Country deleted successfully'),
        ]);
    }

    /**
     * Get active countries for dropdown.
     */
    public function getActiveCountries()
    {
        $countries = Country::active()->orderBy('name_' . app()->getLocale())->get(['id', 'name', 'nicename']);

        return response()->json($countries);
    }
}
