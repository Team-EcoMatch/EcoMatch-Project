<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        return redirect()->route('login')->with('message', 'Has cerrado sesión correctamente. ¡Hasta pronto!');
    }
}