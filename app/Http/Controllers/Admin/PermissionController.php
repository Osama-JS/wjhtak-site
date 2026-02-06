<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Permission::count(),
            'in_use' => Permission::has('roles')->count(),
            'not_in_use' => Permission::doesntHave('roles')->count()
        ];
        return view('admin.permissions.index', compact('stats'));
    }

    public function getData()
    {
        $permissions = Permission::all();
        return response()->json([
            'data' => $permissions->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'name' => '<strong>' . $permission->name . '</strong>',
                    'created_at' => $permission->created_at->format('Y-m-d'),
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editPermission(' . $permission->id . ')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="deletePermission(' . $permission->id . ')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        try {
            $permission = Permission::create(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission'
            ], 500);
        }
    }

    public function edit(Permission $permission)
    {
        return response()->json([
            'success' => true,
            'permission' => $permission
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        try {
            $permission->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission'
            ], 500);
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission'
            ], 500);
        }
    }
}
