<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.roles.index', compact('permissions'));
    }

    public function getData()
    {
        $roles = Role::with('permissions')->get();
        return response()->json([
            'data' => $roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => '<strong>' . $role->name . '</strong>',
                    'permissions' => $role->permissions->map(function($permission) {
                        return '<span class="badge badge-outline-primary me-1">' . $permission->name . '</span>';
                    })->implode(''),
                    'permissions_count' => '<span class="badge badge-info">' . $role->permissions->count() . '</span>',
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editRole(' . $role->id . ')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="deleteRole(' . $role->id . ')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Role $role)
    {
        return response()->json([
            'success' => true,
            'role' => $role,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role'
            ], 500);
        }
    }
}
