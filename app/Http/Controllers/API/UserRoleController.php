<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    private function formatUser(User $user)
    {
        $user->loadMissing('roles');
        $roleNames = $user->roles->pluck('name')->values();

        return [
            'id' => $user->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roleNames,
            'roles_display' => $roleNames->implode(', '),
        ];
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'email'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query = User::with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $total = User::count();
        $filtered = $query->count();

        $users = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get()
            ->map(fn (User $user) => $this->formatUser($user));

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $users,
        ]);
    }

    public function userOptions()
    {
        return User::orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'value' => $user->id,
                'label' => $user->name . ' (' . $user->email . ')',
            ]);
    }

    public function roleOptions()
    {
        return Role::orderBy('name')
            ->get(['name as value', 'name as label']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ], [
            'user_id.required' => 'User is required',
            'user_id.exists' => 'Selected user is invalid',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->syncRoles($validated['roles'] ?? []);

        return $this->formatUser($user);
    }

    public function show($id)
    {
        return $this->formatUser(User::with('roles')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles($validated['roles'] ?? []);

        return $this->formatUser($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles([]);

        return response()->json(['message' => 'Roles removed']);
    }
}
