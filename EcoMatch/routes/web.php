<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';


Route::middleware(['auth', 'verified'])->group(function(){


Route::get('/publicaciones', [PublicacionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('publicaciones.index');

Route::get('/empresas/{id}edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empressas.update');
Route::get('/empresas{id}', [EmpresaController::class, 'show'])->name('empresas.show');


Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');

Route::get('/publicaciones', [PublicacionController::class, 'index'])->name('publicaciones.index');
Route::get('/publicaciones/create', [PublicacionController::class, 'create'])->name('publicaciones.create');
Route::post('/publicaciones', [PublicacionController::class, 'store'])->name('publicaciones.store');
Route::get('/publicaciones/{id}/edit', [PublicacionController::class, 'edit'])->name('publicaciones.edit');
Route::put('/publicaciones/{id}', [PublicacionController::class, 'update'])->name('publicaciones.update');
Route::delete('/publicaciones/{id}', [PublicacionController::class, 'destroy'])->name('publicaciones.destroy');


Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
Route::put('/solicitudes/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');

Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');
});

