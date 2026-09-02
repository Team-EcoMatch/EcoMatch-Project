<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ================================================================
        // 1. EMPRESAS
        // ================================================================
        
        $empresas = [
            [
                'nombreEmpresa' => 'Recicladora del Norte',
                'direccion' => 'Calle 80 # 12-34, Bogotá',
                'email' => 'contacto@recicladoranorte.com',
                'telefono' => '3001234567',
                'tipoEmpresa' => 'Reciclaje',
                'latitud' => 4.701123,
                'longitud' => -74.045678,
                'radioOperacion' => 30.00,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreEmpresa' => 'EcoEmpresa SAS',
                'direccion' => 'Carrera 15 # 88-45, Bogotá',
                'email' => 'info@ecoempresa.com',
                'telefono' => '3109876543',
                'tipoEmpresa' => 'Gestión Ambiental',
                'latitud' => 4.678902,
                'longitud' => -74.056789,
                'radioOperacion' => 25.00,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreEmpresa' => 'Plásticos del Sur',
                'direccion' => 'Calle 30 # 45-67, Cali',
                'email' => 'ventas@plasticossur.com',
                'telefono' => '3204567890',
                'tipoEmpresa' => 'Plásticos',
                'latitud' => 3.451234,
                'longitud' => -76.567890,
                'radioOperacion' => 40.00,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreEmpresa' => 'Metales Industriales',
                'direccion' => 'Carrera 50 # 20-10, Medellín',
                'email' => 'contacto@metalesind.com',
                'telefono' => '3157890123',
                'tipoEmpresa' => 'Metales',
                'latitud' => 6.234567,
                'longitud' => -75.567890,
                'radioOperacion' => 35.00,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreEmpresa' => 'Cartones del Caribe',
                'direccion' => 'Calle 20 # 10-05, Barranquilla',
                'email' => 'info@cartonescaribe.com',
                'telefono' => '3051234567',
                'tipoEmpresa' => 'Papel y Cartón',
                'latitud' => 10.987654,
                'longitud' => -74.789012,
                'radioOperacion' => 50.00,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('empresa')->insert($empresas);

        // ================================================================
        // 2. CATEGORÍAS
        // ================================================================

        // Obtener IDs de empresas
        $empresaIds = DB::table('empresa')->pluck('idempresa')->toArray();

        $categorias = [
            ['nombre' => 'Plásticos', 'descripcion' => 'Todo tipo de plásticos industriales', 'idempresa' => $empresaIds[0]],
            ['nombre' => 'Papel y Cartón', 'descripcion' => 'Papel, cartón y derivados', 'idempresa' => $empresaIds[0]],
            ['nombre' => 'Metales', 'descripcion' => 'Metales ferrosos y no ferrosos', 'idempresa' => $empresaIds[0]],
            ['nombre' => 'Plásticos', 'descripcion' => 'Plásticos reciclables', 'idempresa' => $empresaIds[1]],
            ['nombre' => 'Vidrio', 'descripcion' => 'Vidrio reciclable', 'idempresa' => $empresaIds[1]],
            ['nombre' => 'Plásticos PET', 'descripcion' => 'Botellas y envases PET', 'idempresa' => $empresaIds[2]],
            ['nombre' => 'Plásticos PVC', 'descripcion' => 'Tuberías y perfiles PVC', 'idempresa' => $empresaIds[2]],
            ['nombre' => 'Chatarra', 'descripcion' => 'Chatarra de hierro y acero', 'idempresa' => $empresaIds[3]],
            ['nombre' => 'Cobre', 'descripcion' => 'Cableado y tuberías de cobre', 'idempresa' => $empresaIds[3]],
            ['nombre' => 'Cartón', 'descripcion' => 'Cartón corrugado y prensado', 'idempresa' => $empresaIds[4]],
            ['nombre' => 'Papel', 'descripcion' => 'Papel blanco y periódico', 'idempresa' => $empresaIds[4]],
        ];

        DB::table('categorias')->insert($categorias);

        // ================================================================
        // 3. PUBLICACIONES
        // ================================================================

        // Obtener IDs de empresas y categorías
        $empresaIds = DB::table('empresa')->pluck('idempresa')->toArray();
        $categoriaIds = DB::table('categorias')->pluck('idcategorias')->toArray();

        $publicaciones = [
            [
                'nombre' => 'Cartón corrugado',
                'descripcion' => 'Cajas de cartón corrugado en buen estado, ideales para embalaje',
                'cantidad' => 500.00,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Semanal',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Carton+Corrugado',
                'idempresa' => $empresaIds[0],
                'idcategorias' => $categoriaIds[1], // Papel y Cartón
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Botellas PET',
                'descripcion' => 'Botellas de plástico transparente prensadas y limpias',
                'cantidad' => 150.50,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Mensual',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Botellas+PET',
                'idempresa' => $empresaIds[1],
                'idcategorias' => $categoriaIds[3], // Plásticos
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tubos PVC',
                'descripcion' => 'Sobrantes de tubería PVC de construcción, varios diámetros',
                'cantidad' => 30.00,
                'unidadMedida' => 'Unidades',
                'frecuencia' => 'Único',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Tubos+PVC',
                'idempresa' => $empresaIds[2],
                'idcategorias' => $categoriaIds[6], // Plásticos PVC
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Chatarra de hierro',
                'descripcion' => 'Chatarra de hierro y acero, ideal para fundición',
                'cantidad' => 1000.00,
                'unidadMedida' => 'Toneladas',
                'frecuencia' => 'Mensual',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Chatarra+Hierro',
                'idempresa' => $empresaIds[3],
                'idcategorias' => $categoriaIds[7], // Chatarra
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cable de cobre',
                'descripcion' => 'Cableado de cobre recuperado, pelado y limpio',
                'cantidad' => 75.00,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Semanal',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Cable+Cobre',
                'idempresa' => $empresaIds[3],
                'idcategorias' => $categoriaIds[8], // Cobre
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cartón prensado',
                'descripcion' => 'Cartón prensado en pacas, listo para reciclaje',
                'cantidad' => 800.00,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Semanal',
                'estado' => 'Disponible',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Carton+Prensado',
                'idempresa' => $empresaIds[4],
                'idcategorias' => $categoriaIds[9], // Cartón
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Papel blanco A4',
                'descripcion' => 'Papel blanco de oficina, reciclable y limpio',
                'cantidad' => 200.00,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Mensual',
                'estado' => 'Pendiente',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Papel+A4',
                'idempresa' => $empresaIds[0],
                'idcategorias' => $categoriaIds[1], // Papel y Cartón
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Vidrio reciclable',
                'descripcion' => 'Vidrio de botellas y envases, clasificado por color',
                'cantidad' => 300.00,
                'unidadMedida' => 'Kilogramos',
                'frecuencia' => 'Mensual',
                'estado' => 'Inactivo',
                'urlImagen' => 'https://via.placeholder.com/600x400/cccccc/ffffff?text=Vidrio',
                'idempresa' => $empresaIds[1],
                'idcategorias' => $categoriaIds[4], // Vidrio
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('publicaciones')->insert($publicaciones);

        // ================================================================
        // 4. ROLES
        // ================================================================

        $roles = [];
        foreach ($empresaIds as $id) {
            $roles[] = [
                'tipo' => 'admin_empresa',
                'idempresa' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $roles[] = [
                'tipo' => 'usuario_empresa',
                'idempresa' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        // Rol global (superadmin)
        $roles[] = [
            'tipo' => 'superadmin',
            'idempresa' => $empresaIds[0], // asociado a la primera empresa
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('roles')->insert($roles);

        // ================================================================
        // 5. USUARIOS
        // ================================================================

        $roleIds = DB::table('roles')->pluck('idroles')->toArray();
        
        // Usuario superadmin
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@ecomatch.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'current_team_id' => null,
                'idEmpresa' => $empresaIds[0],
                'idRol' => $roleIds[count($roleIds) - 1], // último rol (superadmin)
                'estado' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Empresa User',
                'email' => 'empresa@ecomatch.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'current_team_id' => null,
                'idEmpresa' => $empresaIds[0],
                'idRol' => $roleIds[0], // admin_empresa
                'estado' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usuario Normal',
                'email' => 'usuario@ecomatch.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'current_team_id' => null,
                'idEmpresa' => $empresaIds[1],
                'idRol' => $roleIds[1], // usuario_empresa
                'estado' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}