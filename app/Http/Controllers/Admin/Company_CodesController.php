<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company_Codes;
use App\Models\Company;

class Company_CodesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $stats = [
            'total' => Company_Codes::count(),
            'active' => Company_Codes::where('active', true)->count(),
            'inactive' => Company_Codes::where('active', false)->count(),
        ];
        return view('admin.company-codes.index', compact('companies', 'stats'));
    }



    public function getData()
    {
         $codes = Company_Codes::with('company')->get();

        return response()->json([
            'data' => $codes->map(function ($code) {
                return [
                    'company' => $code->company->name ?? '-',
                    'code'    => $code->code,
                    'type'    => ucfirst($code->type),
                    'value'   => $code->type === 'percentage'
                        ? $code->value.' %'
                        : $code->value,
                    'status'  => $code->active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>',
                    'actions' => '
                        <button class="btn btn-sm btn-primary" onclick="editCode('.$code->id.')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="toggleCodeStatus('.$code->id.')">
                            <i class="fas fa-ban"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCode('.$code->id.')">
                            <i class="fas fa-trash"></i>
                        </button>
                    ',
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
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code'       => 'required|string|unique:company_codes,code',
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0',
        ]);

        $data['active'] = true;

        Company_Codes::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Code created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company_Codes $company_code)
    {
        return response()->json([
            'success' => true,
            'Company_Codes' => $company_code,
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
    public function update(Request $request, Company_Codes $company_code)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code'       => 'required|string|unique:company_codes,code,' . $company_code->id,
            'type'       => 'required|in:fixed,percentage',
            'value'      => 'required|numeric|min:0',
        ]);

        $company_code->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Code updated successfully'
        ]);
    }

    public function toggleStatus(Company_Codes $company_code)
    {
        $company_code->active = !$company_code->active;
        $company_code->save();

        return response()->json([
            'success' => true,
            'message' => 'Code status updated successfully',
            'status'  => $company_code->active ? 'Active' : 'Inactive'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company_Codes $company_code)
    {
        $company_code->delete();

        return response()->json([
            'success' => true,
            'message' => 'Code deleted successfully'
        ]);
    }
}
