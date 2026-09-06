<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $team = $user ? $user->currentTeam : null;

        if ($team) {
            return redirect()->route('dashboard', ['current_team' => $team->slug])
                             ->with('message', '¡Bienvenido de nuevo, ' . $user->name . '!');
        }

        return redirect()->route('home');
    }
}