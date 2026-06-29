<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $columns = ['id', 'name', 'description'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query = Department::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        $total = Department::count();
        $filtered = $query->count();

        $departments = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $departments,
        ]);
    }

    public function options()
    {
        return Department::orderBy('name')
            ->get(['id as value', 'name as label']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:departments,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Department name is required',
            'name.min' => 'Department name must be at least 2 characters',
            'name.unique' => 'This department already exists',
        ]);

        return Department::create($validated);
    }

    public function show($id)
    {
        return Department::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Department name is required',
            'name.min' => 'Department name must be at least 2 characters',
            'name.unique' => 'This department already exists',
        ]);

        $department->update($validated);

        return $department;
    }

    public function destroy($id)
    {
        Department::destroy($id);

        return response()->json(['message' => 'Deleted']);
    }
}
