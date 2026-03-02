<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyAgentController extends Controller
{
    public function index(Company $company)
    {
        $countries = \App\Models\Country::active()->get();
        return view('admin.companies.agents', compact('company', 'countries'));
    }

    public function getData(Company $company)
    {
        $agents = $company->agents;

        return response()->json([
            'data' => $agents->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'name' => $agent->first_name . ' ' . $agent->last_name,
                    'phone' => '<span dir="ltr">+' . $agent->country_code . ' ' . $agent->phone . '</span>',
                    'email' => $agent->email,
                    'status' => $agent->status === 'active'
                        ? '<span class="badge bg-success">'.__('Active').'</span>'
                        : '<span class="badge bg-danger">'.__('Inactive').'</span>',
                    'actions' => '
                        <button onclick="editAgent('.$agent->id.')" class="btn btn-primary btn-xs" title="'.__('Edit').'">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button onclick="toggleAgentStatus('.$agent->id.')" class="btn btn-warning btn-xs" title="'.__('Status').'">
                            <i class="fa fa-ban"></i>
                        </button>
                        <button onclick="deleteAgent('.$agent->id.')" class="btn btn-danger btn-xs" title="'.__('Delete').'">
                            <i class="fa fa-trash"></i>
                        </button>'
                ];
            })
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'country_code' => 'required|string|max:10',
            'phone'        => 'required|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $agent = User::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'         => $validated['email'],
            'country_code' => $validated['country_code'],
            'phone'        => $validated['phone'],
            'password'     => Hash::make($validated['password']),
            'user_type'    => User::TYPE_AGENT,
            'company_id'   => $company->id,
            'status'       => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Agent added successfully')
        ]);
    }

    public function edit(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,'.$user->id,
            'country_code' => 'required|string|max:10',
            'phone'        => 'required|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'         => $validated['email'],
            'country_code' => $validated['country_code'],
            'phone'        => $validated['phone'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Agent updated successfully')
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => __('Agent status updated successfully')
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->user_type !== User::TYPE_AGENT) {
            return response()->json(['success' => false, 'message' => 'Invalid user type'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('Agent deleted successfully')
        ]);
    }
}
