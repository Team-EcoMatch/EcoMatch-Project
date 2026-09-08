<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('dashboard', function ($user) {
    return true; 
});
