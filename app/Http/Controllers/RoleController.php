<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('modules')->get();
        $modules = Module::all();
        return view('management.roles.index', compact('roles', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:roles,name'],
            'display_name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        Role::create([
            'name' => strtolower(str_replace(' ', '_', $request->name)),
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Role baru berhasil ditambahkan.');
    }

    public function grantModules(Request $request, Role $role)
    {
        $request->validate([
            'modules' => ['nullable', 'array'],
            'modules.*' => ['exists:modules,id'],
        ]);

        // Sync modul yang di-grant ke role
        $role->modules()->sync($request->input('modules', []));

        return back()->with('success', 'Hak akses modul untuk role ' . $role->display_name . ' berhasil diperbarui.');
    }
}