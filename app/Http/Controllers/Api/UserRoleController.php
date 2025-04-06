<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    // Zoznam všetkých rolí
    public function index()
    {
        return response()->json(UserRole::with('users')->get());
    }

    // Vytvorenie novej role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'privilege' => 'required|string|max:255',
        ]);

        $role = UserRole::create($validated);

        return response()->json($role, 201);
    }

    // Zobrazenie jednej role podľa ID
    public function show($id)
    {
        $role = UserRole::with('users')->findOrFail($id);
        return response()->json($role);
    }

    // Update existujúcej role
    public function update(Request $request, $id)
    {
        $role = UserRole::findOrFail($id);

        $validated = $request->validate([
            'role_name' => 'sometimes|string|max:255',
            'privilege' => 'sometimes|string|max:255',
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    // Vymazanie role
    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Rola bola vymazaná.']);
    }
}
