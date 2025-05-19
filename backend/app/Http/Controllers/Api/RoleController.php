<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $roles = $query->paginate($perPage);

        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request)
    {
        $data = $request->validated();
        
        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        $role->load('permissions');

        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $data = $request->validated();
        
        $role->update([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        $role->load('permissions');

        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if the role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role with associated users.'
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json(null, 204);
    }
}
