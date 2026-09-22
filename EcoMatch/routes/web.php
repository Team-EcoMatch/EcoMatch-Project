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
use App\Http\Controllers\ReporteController;

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

    Route::get('/buscar', [PublicacionController::class, 'search'])->name('publicaciones.search');

    Route::get('/chat/{solicitud}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{solicitud}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/{solicitud}/bloquear', [ChatController::class, 'bloquear'])->name('chat.bloquear');
    Route::post('/chat/{solicitud}/desbloquear', [ChatController::class, 'desbloquear'])->name('chat.desbloquear');
    Route::get('/chats', [ChatController::class, 'listaChats'])->name('chat.lista');
    Route::post('/chat/{solicitud}/marcar-leido', [ChatController::class, 'marcarLeido'])->name('chat.marcarLeido');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/export/pdf', [ReporteController::class, 'exportPdf'])->name('reportes.export.pdf');
    Route::get('/reportes/export/excel', [ReporteController::class, 'exportExcel'])->name('reportes.export.excel');

    Route::get('/admin/reportes', [ReporteController::class, 'index'])->name('admin.reportes.index');

    Route::post('/notificaciones/marcar-leida/{idnotificacion}', function ($idnotificacion) {
        $user = Auth::user();

        $notificacion = \App\Models\NotificacionBloqueo::where('idnotificacion', $idnotificacion)
            ->where('idempresa_destinataria', $user->idempresa)
            ->first();

        if ($notificacion) {
            $notificacion->update(['leida' => true]);
        }

        return back();
    })->name('notificaciones.marcarLeida');

    Route::post('/notificaciones/marcar-todas-leidas', function () {
        $user = Auth::user();

        \App\Models\NotificacionBloqueo::where('idempresa_destinataria', $user->idempresa)
            ->where('leida', false)
            ->update(['leida' => true]);

        return back();
    })->name('notificaciones.marcarTodasLeidas');

    Route::post('/notificaciones/marcar-leida-empresa/{idempresa}', function ($idempresa) {
        $user = Auth::user();

        \App\Models\NotificacionBloqueo::where('idempresa_destinataria', $user->idempresa)
            ->where('idempresa_bloqueadora', $idempresa)
            ->where('leida', false)
            ->update(['leida' => true]);

        return back();
    })->name('notificaciones.marcarLeidaEmpresa');

    
});

Route::middleware(['auth', 'verified', 'role:Jefe'])->group(function () {

    Route::post('/publicaciones/{id}/approve', [PublicacionController::class, 'approve'])->name('publicaciones.approve');

    Route::get('/empresas/{id}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
    Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
    Route::get('/empresas/{id}', [EmpresaController::class, 'show'])->name('empresas.show');

    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');

    Route::get('/admin/publicaciones', [AdminPublicacionController::class, 'index'])->name('admin.publicaciones.index');
    Route::patch('/admin/publicaciones/{id}/estado', [AdminPublicacionController::class, 'updateEstado'])->name('admin.publicaciones.updateEstado');
    Route::patch('/admin/publicaciones/{id}/approve', [AdminPublicacionController::class, 'approve'])->name('admin.publicaciones.approve');
    Route::patch('/admin/publicaciones/{id}/reject', [AdminPublicacionController::class, 'reject'])->name('admin.publicaciones.reject');
    Route::delete('/admin/publicaciones/{id}', [AdminPublicacionController::class, 'destroy'])->name('admin.publicaciones.destroy');

    Route::get('/historial', [SolicitudController::class, 'historial'])->name('historial.index');

    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});
