<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private function formatRole(Role $role)
    {
        $role->loadMissing('permissions');
        $permissionNames = $role->permissions->pluck('name')->values();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $permissionNames,
            'permissions_display' => $permissionNames->implode(', '),
        ];
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'guard_name'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query = Role::with('permissions');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        $total = Role::count();
        $filtered = $query->count();

        $roles = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(fn (Role $role) => $this->formatRole($role));

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.required' => 'Role name is required',
            'name.unique' => 'This role already exists',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions(Permission::whereIn('name', $validated['permissions'] ?? [])->get());

        return $this->formatRole($role);
    }

    public function show($id)
    {
        return $this->formatRole(Role::with('permissions')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.required' => 'Role name is required',
            'name.unique' => 'This role already exists',
        ]);

        $role->update([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(Permission::whereIn('name', $validated['permissions'] ?? [])->get());

        return $this->formatRole($role);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['admin', 'non-admin'])) {
            return response()->json([
                'message' => 'Default roles cannot be deleted.',
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
