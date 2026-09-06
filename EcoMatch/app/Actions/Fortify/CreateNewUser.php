<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Team;
use App\Models\Membership;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nombreEmpresa' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:250'],
            'telefono' => ['required', 'string', 'max:20'],
            'tipoEmpresa' => ['required', 'string', 'max:100'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            
            $empresa = Empresa::create([
                'nombreEmpresa' => $input['nombreEmpresa'],
                'direccion' => $input['direccion'],
                'email' => $input['email'],
                'telefono' => $input['telefono'],
                'tipoEmpresa' => $input['tipoEmpresa'],
                'radioOperacion' => 10,
                'estado' => 1,
            ]);

            $rolJefe = Rol::create([
                'tipo' => 'Jefe',
                'idempresa' => $empresa->idempresa,
            ]);

            Rol::create([
                'tipo' => 'Empresa',
                'idempresa' => $empresa->idempresa,
            ]);

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'idempresa' => $empresa->idempresa,
                'idRol' => $rolJefe->idroles,
            ]);

            $team = Team::forceCreate([
                'name' => $empresa->nombreEmpresa,
                'slug' => \Illuminate\Support\Str::slug($empresa->nombreEmpresa),
                'is_personal' => true,
            ]);

            $user->current_team_id = $team->id;
            $user->save();

            Membership::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'role' => 'admin',
            ]);

            return $user;
        });
    }
}