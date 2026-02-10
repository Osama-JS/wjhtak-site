<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display the banners management page.
     */
    public function index()
    {
        $trips = Trip::all();
        $stats = [
            'total' => Banner::count(),
            'active' => Banner::where('active', true)->count(),
            'inactive' => Banner::where('active', false)->count(),
        ];
        return view('admin.banners.index', compact('stats','trips'));
    }

    /**
     * Get banners data for DataTable.
     */
    public function getData()
    {
        $banners = Banner::with(['trip'])->ordered()->get();

        $data = $banners->map(function ($banner) {
            return [
                'id' => $banner->id,
                'image_url' => '<img src="' . $banner->image_url . '" 
                     alt="' . $banner->title_en . '" 
                     class="rounded" width="100" height="60" 
                     style="object-fit: cover;" 
                     onerror="this.onerror=null; this.src=\'' . asset('images/flags/default.jpg') . '\';">',
                'title_ar' => $banner->title_ar ?? '---',
                'title_en' => $banner->title_en ?? '---',
                'link' => $banner->link ? '<a href="' . $banner->link . '" target="_blank" class="text-primary"><i class="fas fa-external-link-alt"></i></a>' : '---',
                'trip' => $banner->trip
                          ?  '<span>'. $banner->trip->title .'</span>' : '...',
                'sort_order' => '<span class="badge badge-light">' . $banner->sort_order . '</span>',
                'status' => $banner->active
                    ? '<span class="badge badge-success">' . __('Active') . '</span>'
                    : '<span class="badge badge-danger">' . __('Inactive') . '</span>',
                'actions' => $this->getActionButtons($banner),
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Generate action buttons HTML.
     */
    private function getActionButtons($banner): string
    {
        $editBtn = '<button class="btn btn-primary btn-sm me-1" onclick="editBanner(' . $banner->id . ')" title="' . __('Edit') . '"><i class="fas fa-edit"></i></button>';
        $toggleBtn = '<button class="btn btn-' . ($banner->active ? 'warning' : 'success') . ' btn-sm me-1" onclick="toggleBannerStatus(' . $banner->id . ')" title="' . __('Toggle Status') . '"><i class="fas fa-' . ($banner->active ? 'ban' : 'check') . '"></i></button>';
        $deleteBtn = '<button class="btn btn-danger btn-sm" onclick="deleteBanner(' . $banner->id . ')" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>';

        return '<div class="d-flex">' . $editBtn . $toggleBtn . $deleteBtn . '</div>';
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'trip_id'            => 'required|exists:trips,id',
            'active' => 'boolean',
        ]);
         
        //  return response()->json($request->all());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title_ar', 'title_en', 'description_ar', 'description_en', 'link','trip_id']);
        $data['active'] = $request->boolean('active', true);
        $data['sort_order'] = $request->input('sort_order', Banner::max('sort_order') + 1);
        $data['admin_id'] = auth()->id();

       
        // Handle image upload
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('banners', 'public');
        }

        Banner::create($data);

         dd($data);


        return response()->json([
            'success' => true,
            'message' => __('Banner added successfully'),
        ]);
    }

    /**
     * Display the specified banner.
     */
    public function show(Banner $banner)
    {
        return response()->json([
            'success' => true,
            'banner' => $banner,
            'image_url' => $banner->image_url,
        ]);
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'link' => 'nullable|url|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'trip_id'            => 'required|exists:trips,id',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title_ar', 'title_en', 'description_ar', 'description_en', 'link', 'trip_id','sort_order',]);
        $data['active'] = $request->boolean('active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        
        return response()->json([
            'success' => true,
            'message' => __('Banner updated successfully'),
        ]);
    }

    /**
     * Toggle banner status.
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->update(['active' => !$banner->active]);

        return response()->json([
            'success' => true,
            'message' => $banner->active ? __('Banner activated') : __('Banner deactivated'),
        ]);
    }

    /**
     * Reorder banners.
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sort_order' => 'required|array',
            'sort_order.*' => 'integer|exists:banners,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        foreach ($request->order as $index => $id) {
            Banner::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Banners reordered successfully'),
        ]);
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner)
    {
        // Delete image if exists
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => __('Banner deleted successfully'),
        ]);
    }
}
