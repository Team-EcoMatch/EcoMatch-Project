<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        $idEmpresa = Auth::user()->idempresa;

        $rolEmpleado = Rol::where('idempresa', $idEmpresa)->where('tipo', 'Empresa')->first();

        $empleados = User::where('idempresa', $idEmpresa)
            ->where('idRol', $rolEmpleado->idroles ?? 0)
            ->get();

        return Inertia::render('Empleados/Index', [
            'empleados' => $empleados
        ]);
    }

        public function store(Request $request)
    {
        $idEmpresa = Auth::user()->idempresa;
        $jefe = Auth::user(); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $rolEmpleado = Rol::where('idempresa', $idEmpresa)->where('tipo', 'Empresa')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'idempresa' => $idEmpresa,
            'idRol' => $rolEmpleado->idroles,
            'email_verified_at' => now(),
            'current_team_id' => $jefe->current_team_id, 
        ]);

        \App\Models\Membership::create([
            'team_id' => $jefe->current_team_id,
            'user_id' => $user->id,
            'role' => 'member', 
        ]);

        return redirect()->back()->with('message', 'Empleado agregado exitosamente.');
    }
}