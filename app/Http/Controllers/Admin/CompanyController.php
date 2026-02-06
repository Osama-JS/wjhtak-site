<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => Company::count(),
            'active' => Company::where('active', true)->count(),
            'inactive' => Company::where('active', false)->count(),
        ];
        return view('admin.companies.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $companies = Company::all(); // أو where حسب حاجتك

        return response()->json([
            'data' => $companies->map(function ($company) {
                return [
                    'name' => $company->name,
                    'email' => $company->email,
                    'phone' => $company->phone,
                    'notes' => $company->notes,
                    'status' => $company->active
                        ? '<span class="badge bg-success">'.__('Active').'</span>'
                        : '<span class="badge bg-danger">'.__('Inactive').'</span>',
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editCompany('.$company->id.')" class="btn btn-primary btn-xs me-1">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button onclick="togglecompanytatus('.$company->id.')" class="btn btn-warning btn-xs me-1">
                                <i class="fas fa-ban"></i>
                            </button>
                            <button onclick="deletecompanie('.$company->id.')" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>'
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|',
            'phone'  => 'nullable|string|max:100',
            'notes'  => 'required|',
            'status' => 'required|in:active,inactive',
        ]);

        Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
         return response()->json([
            'success' => true,
            'Company' => $company,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|',
            'phone' => 'nullable|phone|',
            'notes' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'notes']);
        $data['active'] = $request->status === 'active';
        $company->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully'
        ]);
    }

     /**
     * Toggle user status.
     */
    public function toggleStatus(Company $company)
    {
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $updated = $company->update(['status' => $newStatus]);

        // dd([
        //     'user_id' => $user->id,
        //     'new_status' => $newStatus,
        //     'updated' => $updated
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Company status updated to ' . $newStatus,
            'status' => $newStatus
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
       try {

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }
}
