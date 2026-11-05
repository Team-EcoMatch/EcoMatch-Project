<div align="center">

# ♻️ EcoMatch

### Plataforma de matcheo de materiales entre empresas

**Conecta empresas que tienen materiales sobrantes con empresas que los necesitan.**
Menos desperdicio. Más economía circular. 🌱

</div>

---

## 📖 Tabla de contenidos

- [📖 Acerca del proyecto](#-acerca-del-proyecto)
- [✨ Características principales](#-características-principales)
- [🧩 Módulos del sistema](#-módulos-del-sistema)
- [🛠️ Stack tecnológico](#️-stack-tecnológico)
- [🚀 Instalación](#-instalación)
- [⚙️ Configuración de servicios externos](#️-configuración-de-servicios-externos)
- [📁 Estructura del proyecto](#-estructura-del-proyecto)
- [🗺️ Roadmap de desarrollo](#️-roadmap-de-desarrollo)
- [🧑‍💻 Convenciones de código](#-convenciones-de-código)
- [🤝 Cómo contribuir](#-cómo-contribuir)
- [👥 Equipo](#-equipo)
- [📄 Licencia](#-licencia)

---

## 📖 Acerca del proyecto

**EcoMatch** es una plataforma web multi-tenant (una cuenta por empresa) que permite
publicar materiales disponibles (excedentes, subproductos, residuos reutilizables),
buscarlos por categoría, ubicación o empresa, abrir **solicitudes** entre empresas,
**chatear en tiempo real** para coordinar el intercambio y visualizar todo en un
**mapa interactivo**.

El proyecto se desarrolla en **5 sesiones de 7 horas (35 h totales)** y cubre **9 módulos funcionales**.

> Repositorio: [github.com/Team-EcoMatch/EcoMatch-Project](https://github.com/Team-EcoMatch/EcoMatch-Project)

---

## ✨ Características principales

- 🏢 **Multi-tenant con Teams** — cada empresa es un equipo aislado con sus propios datos.
- 🔐 **Autenticación con Laravel Fortify** — login, 2FA y recuperación de contraseña.
- 👥 **Roles y permisos** — `Jefe` (aprueba, invita, elimina) y `Empleado` (opera).
- 📦 **Catálogo de materiales con imágenes** — subida directa a **Cloudinary**.
- 🔎 **Buscador multi-criterio** — categoría, empresa, ubicación y estado.
- 🤝 **Workflow de solicitudes** — `pendiente → aceptada → rechazada → completada → cancelada`.
- 💬 **Chat en tiempo real** — Laravel Reverb + Pusher + Laravel Echo.
- 🚫 **Bloqueo de empresas** con notificaciones persistentes.
- 🗺️ **Mapa interactivo** con Leaflet y marcadores filtrables por categoría.
- 📄 **Exportaciones** — PDF (DomPDF) y Excel (Maatwebsite).
- 🧭 **Rutas type-safe** con Laravel Wayfinder.
- ⚡ **SPA moderna** vía Inertia.js (sin API REST separada).

---

## 🧩 Módulos del sistema

El proyecto está compuesto por **9 módulos funcionales**, distribuidos en 5 sesiones:

| #  | Módulo               | Descripción                                              | Sesión |
|----|----------------------|----------------------------------------------------------|--------|
| 1  | **Dashboard**        | KPIs del team actual, accesos rápidos y resumen operativo. | S1     |
| 2  | **Perfil de Empresa**| Edición de logo, RUC, dirección, rubro y datos públicos.   | S1     |
| 3  | **Categorías**       | CRUD RESTful de categorías (admin por Jefe).              | S2     |
| 4  | **Mis Publicaciones**| Alta de materiales con fotos, estado y aprobación.         | S2     |
| 5  | **Buscar Materiales**| Buscador con filtros combinables, debounce y partial reloads. | S3  |
| 6  | **Solicitudes**      | Máquina de estados entre empresas + historial.            | S3     |
| 7  | **Chats**            | Mensajería bidireccional en tiempo real con canales privados. | S4  |
| 8  | **Mapa Interactivo** | Geolocalización de publicaciones con Leaflet + clustering. | S4     |
| 9  | **Empleados**        | Invitaciones por email, roles, permisos y desactivación.  | S5     |

---

## 🛠️ Stack tecnológico

| Capa             | Tecnología                                                        |
|------------------|-------------------------------------------------------------------|
| **Backend**      | Laravel 13 · PHP 8.3 · Eloquent ORM                               |
| **Frontend**     | Vue 3.5 · Inertia.js 3 · Tailwind CSS 4.1 · Reka UI · VueUse     |
| **Icons**        | lucide-vue-next                                                   |
| **Bundler**      | Vite 8                                                            |
| **Routing TS**   | Laravel Wayfinder                                                 |
| **Auth**         | Laravel Fortify 1.37 + Teams (multi-tenant)                       |
| **Base de datos**| SQLite (dev) / MySQL (prod)                                        |
| **Tiempo real**  | Laravel Reverb 1.11 + Pusher + Laravel Echo                        |
| **Mapas**        | Leaflet 1.9 + @vue-leaflet/vue-leaflet                            |
| **Archivos**     | Cloudinary PHP SDK 3.1                                            |
| **Exportaciones**| barryvdh/laravel-dompdf 3.1 (PDF) · maatwebsite/excel 4.0 (Excel) |
| **UI feedback**  | SweetAlert2 · notie · vue-sonner                                  |
| **Calidad**      | Pint (formatter) · PHPStan (análisis estático) · Pest (tests)    |
| **CI/CD**        | GitHub Actions                                                    |

---

## 🚀 Instalación

### ✅ Requisitos previos

- **PHP 8.3+** con extensiones: `pdo_sqlite`, `mbstring`, `openssl`, `pdo`, `json`, `fileinfo`
- **Composer 2.x**
- **Node.js 20+** y `npm` (o `pnpm` / `bun`)
- **Git** y cuenta en GitHub con acceso al repo
- Cuenta en **[Cloudinary](https://cloudinary.com)** (para imágenes)
- (Opcional) Cuenta en **[Pusher](https://pusher.com)** o uso de **Laravel Reverb** local

### 📥 Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/Team-EcoMatch/EcoMatch-Project.git
cd EcoMatch-Project

# 2. Instalar dependencias de PHP y JS
composer install
npm install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos (SQLite por defecto en dev)
touch database/database.sqlite
php artisan migrate --seed

# 5. Levantar el entorno de desarrollo
php artisan serve   # http://localhost:8000
npm run dev          # Vite (HMR) en otra terminal
```

### 👤 Usuario de prueba

Tras el `--seed`, se crean usuarios demo:

| Rol     | Email                    | Contraseña  |
|---------|--------------------------|-------------|
| Jefe    | `jefe@ecomatch.test`     | `password`  |
| Empleado| `empleado@ecomatch.test` | `password`  |

---

## ⚙️ Configuración de servicios externos

### Cloudinary (imágenes)

```env
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```

### Tiempo real (Reverb)

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=tu_app_key
REVERB_APP_ID=tu_app_id
REVERB_APP_SECRET=tu_app_secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
```

Levanta el servidor WebSocket:

```bash
php artisan reverb:start
php artisan queue:listen   # en otra terminal
```

### Pusher (alternativa a Reverb)

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=tu_app_id
PUSHER_APP_KEY=tu_app_key
PUSHER_APP_SECRET=tu_app_secret
PUSHER_APP_CLUSTER=mt1
```

---

## 📁 Estructura del proyecto

```
EcoMatch-Project/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # CategoriaController, PublicacionController,
│   │   │                     # SolicitudController, ChatController,
│   │   │                     # MapaController, EmpleadoController, ...
│   │   ├── Middleware/       # EnsureTeamMembership, RoleMiddleware
│   │   └── Requests/         # CategoriaRequest, PublicacionRequest...
│   ├── Models/               # User, Team, Empresa, Categoria, Publicacion,
│   │                         # Solicitud, Mensaje, NotificacionBloqueo, Empleado
│   ├── Events/               # MessageSent (implements ShouldBroadcast)
│   └── Policies/             # PublicacionPolicy, SolicitudPolicy...
├── database/
│   ├── migrations/           # users, teams, team_user, empresas,
│   │                         # categorias, publicaciones, fotos_publicacion,
│   │                         # solicitudes, mensajes, notificaciones_bloqueo, empleados
│   └── seeders/              # Datos demo
├── resources/
│   ├── js/
│   │   ├── Pages/            # Vistas Inertia (Dashboard, Empresas, Buscar,
│   │   │                     # Solicitudes, Chats, Mapa, Empleados...)
│   │   ├── Components/       # Componentes Vue reutilizables
│   │   └── Layouts/          # AppLayout con sidebar y topbar
│   └── views/                # Blade mail templates
├── routes/
│   ├── web.php               # Rutas Inertia + Wayfinder
│   └── channels.php          # Autorización de canales privados (private-chat.{id})
├── .github/workflows/        # Pipeline CI/CD (lint, tests, build)
├── scripts/                  # Seeders masivos y utilidades
├── composer.json
└── package.json
```

---

## 🗺️ Roadmap de desarrollo

El proyecto se construye en **5 sesiones de 7 horas** cada una (35 h en total). Cada sesión cubre 1–2 módulos y entrega un resultado verificable.

### 🟢 S1 — Dashboard + Perfil de Empresa *(7 h)*
**Énfasis:** Creación del entorno y arquitectura base.

- Entorno levantado (`php artisan serve` + `npm run dev`)
- Dashboard renderizado con KPIs del team actual
- Edición del Perfil de Empresa (logo, RUC, dirección, rubro)
- Login verificado con Laravel Fortify

**Módulos:** `1. Dashboard` · `2. Perfil de Empresa`

---

### 🟢 S2 — Categorías + Mis Publicaciones *(7 h)*
**Énfasis:** Construcción del catálogo de materiales.

- CRUD RESTful de categorías (7 métodos del controlador)
- Form Requests con validación server-side y mensajes en español
- Subida de imágenes a **Cloudinary** (directa y firmada)
- Flujo de aprobación restringido al rol `Jefe` (`PATCH /publicaciones/{id}/approve`)
- Vistas Inertia con formularios create/edit y SweetAlert2

**Módulos:** `3. Categorías` · `4. Mis Publicaciones`

---

### 🟢 S3 — Buscar Materiales + Solicitudes *(7 h)*
**Énfasis:** Implementación de búsqueda y workflow.

- Buscador multi-criterio con `when()` y eager loading (`with()`) — evita N+1
- Máquina de estados de solicitudes: `pendiente → aceptada → rechazada → completada → cancelada`
- Vista `/historial` paginada por empresa logueada
- Vue con `@vueuse/core` (debounce 300 ms) y partial reloads de Inertia
- Laravel Wayfinder para rutas tipadas en TypeScript

**Módulos:** `5. Buscar Materiales` · `6. Solicitudes`

---

### 🟢 S4 — Chats + Mapa Interactivo *(7 h)*
**Énfasis:** Integración de tiempo real y geolocalización.

- **Chat bidireccional en tiempo real** con Laravel Reverb + Pusher + Laravel Echo
- Evento `MessageSent` (implements `ShouldBroadcast`) en canal privado `private-chat.{solicitud_id}`
- Autorización de canales en `routes/channels.php`
- Sistema de **bloqueo de empresas** con notificaciones persistentes
- **Mapa interactivo** Leaflet con marcadores por categoría y filtros laterales

**Módulos:** `7. Chats` · `8. Mapa Interactivo`

---

### 🟢 S5 — Empleados + Integración final *(7 h)*
**Énfasis:** Gestión de equipo y reportes.

- Modelo `Empleado` + `TeamInvitationController` con `accept_token` por email
- Vista `/empleados` con invitación y listado del team
- Reporte **PDF con gráficos** (DomPDF + Chart.js)
- Reporte **Excel** con plantilla (Maatwebsite)
- Pipeline CI/CD verde en GitHub Actions

**Módulos:** `9. Empleados` · `Integración final`

---

## 🧑‍💻 Convenciones de código

- **Laravel Pint** para formateo PHP (`./vendor/bin/pint`)
- **PHPStan** para análisis estático (`./vendor/bin/phpstan analyse`)
- **Pest** para tests (`php artisan test`)
- Commits siguiendo **Conventional Commits** (`feat:`, `fix:`, `docs:`, `refactor:`...)
- Una feature = una rama = un PR (revisión requerida antes de merge a `main`)

```bash
# Antes de un commit
./vendor/bin/pint
npm run lint
php artisan test
```

---

## 🤝 Cómo contribuir

1. Haz **fork** del repositorio (o crea una rama si tienes acceso directo).
2. Crea una rama descriptiva: `git checkout -b feat/modulo-busqueda-avanzada`
3. Desarrolla siguiendo las **convenciones de código**.
4. Verifica localmente: `./vendor/bin/pint && ./vendor/bin/phpstan analyse && php artisan test`
5. Abre un **Pull Request** describiendo el cambio y el módulo al que aporta.

---

## 👥 Equipo

**Organización:** [Team-EcoMatch](https://github.com/Team-EcoMatch)

> Proyecto educativo / académico — construcción guiada en 5 sesiones.

---

## 📄 Licencia

Este proyecto está bajo licencia **MIT**. Puedes usarlo, modificarlo y distribuirlo libremente.

---

<div align="center">

**♻️ Menos desperdicio. Más economía circular.**

Hecho por:
Julián David Reyes Villate
Juan Sebastián Chaparro Salamanca 

</div>
