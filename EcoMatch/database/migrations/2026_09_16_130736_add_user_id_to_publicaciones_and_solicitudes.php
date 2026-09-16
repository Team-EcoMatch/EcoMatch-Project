<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Agregar user_id a publicaciones
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('idcategorias');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
        });

        // Agregar user_id a solicitudes (quién la creó)
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('idEmpresaOrigen');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};