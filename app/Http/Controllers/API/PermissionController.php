<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $columns = ['id', 'name', 'guard_name'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query = Permission::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        $total = Permission::count();
        $filtered = $query->count();

        $permissions = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $permissions,
        ]);
    }

    public function options()
    {
        return Permission::orderBy('name')
            ->get(['id as value', 'name as label']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:permissions,name',
        ], [
            'name.required' => 'Permission name is required',
            'name.unique' => 'This permission already exists',
        ]);

        return Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
    }

    public function show($id)
    {
        return Permission::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:permissions,name,' . $id,
        ], [
            'name.required' => 'Permission name is required',
            'name.unique' => 'This permission already exists',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return $permission;
    }

    public function destroy($id)
    {
        Permission::destroy($id);

        return response()->json(['message' => 'Deleted']);
    }
}
