<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPublicacionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EmpleadoController;

Route::get('/', function () {
    if (Auth::check() && Auth::user()->currentTeam) {
        return redirect()->route('dashboard', ['current_team' => Auth::user()->currentTeam->slug]);
    }
    return inertia('Welcome');
})->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__ . '/settings.php';

// Rutas para TODOS los usuarios logueados (Jefes y Empleados)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/categorias/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
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

    //Busqueda geoespacial
    Route::get('/buscar', [PublicacionController::class, 'search'])->name('publicaciones.search');

    //Rutas para los chats
    Route::get('/chat/{solicitud}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{solicitud}', [ChatController::class, 'store'])->name('chat.store');
});

// Rutas EXCLUSIVAS para el Jefe de Empresa
Route::middleware(['auth', 'verified', 'role:Jefe'])->group(function () {
    
    // Aprobar Publicaciones
    Route::post('/publicaciones/{id}/approve', [PublicacionController::class, 'approve'])->name('publicaciones.approve');

    //perfil de empresas
    Route::get('/empresas/{id}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
    Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::get('/empresas/{id}', [EmpresaController::class, 'show'])->name('empresas.show');

    // Gestionar Empleados
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');

    // Administrar publicaciones 
    Route::get('/admin/publicaciones', [AdminPublicacionController::class, 'index'])->name('admin.publicaciones.index');
    Route::patch('/admin/publicaciones/{id}/estado', [AdminPublicacionController::class, 'updateEstado'])->name('admin.publicaciones.updateEstado');
    Route::patch('/admin/publicaciones/{id}/approve', [AdminPublicacionController::class, 'approve'])->name('admin.publicaciones.approve');
    Route::patch('/admin/publicaciones/{id}/reject', [AdminPublicacionController::class, 'reject'])->name('admin.publicaciones.reject');
    Route::delete('/admin/publicaciones/{id}', [AdminPublicacionController::class, 'destroy'])->name('admin.publicaciones.destroy');


    Route::get('/historial', [SolicitudController::class, 'historial'])->name('historial.index');
});
