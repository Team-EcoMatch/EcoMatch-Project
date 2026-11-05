<a id="top"></a>

<div align="center">

<img src="https://img.shields.io/badge/♻️_EcoMatch-Material_Matching_Platform-22c55e?style=for-the-badge&labelColor=0f172a&color=22c55e" alt="EcoMatch banner"/>
<img src="https://img.shields.io/badge/Economía_Circular-🌱-10b981?style=for-the-badge&labelColor=0f172a"/>

<br/><br/>

<h1>♻️ EcoMatch</h1>

<h3>🌐 Plataforma de matcheo de materiales entre empresas</h3>

<em>Conecta empresas que tienen materiales sobrantes con empresas que los necesitan.</em>

<br/><br/>

<img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white"/>
<img src="https://img.shields.io/badge/Vue.js-3.5-42b883?style=flat-square&logo=vuedotjs&logoColor=white"/>
<img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/Inertia.js-3-6366f1?style=flat-square"/>
<img src="https://img.shields.io/badge/Tailwind_CSS-4.1-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white"/>
<img src="https://img.shields.io/badge/Vite-8-646CFF?style=flat-square&logo=vite&logoColor=white"/>
<br/>
<img src="https://img.shields.io/badge/MySQL_Workbench-4479A1?style=flat-square&logo=mysql&logoColor=white"/>
<img src="https://img.shields.io/badge/Laravel_Reverb-1.11-FF2D20?style=flat-square"/>
<img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=flat-square&logo=leaflet&logoColor=white"/>
<img src="https://img.shields.io/badge/Cloudinary-3448C5?style=flat-square&logo=cloudinary&logoColor=white"/>
<img src="https://img.shields.io/badge/Laravel_Fortify-1.37-FF2D20?style=flat-square"/>
<img src="https://img.shields.io/badge/License-MIT-22c55e?style=flat-square"/>

<br/><br/>

<img src="https://img.shields.io/github/stars/Team-EcoMatch/EcoMatch-Project?style=social"/>
<img src="https://img.shields.io/github/forks/Team-EcoMatch/EcoMatch-Project?style=social"/>
<img src="https://img.shields.io/github/issues/Team-EcoMatch/EcoMatch-Project?style=social"/>
<img src="https://img.shields.io/github/last-commit/Team-EcoMatch/EcoMatch-Project?style=flat-square&label=Último_commit"/>

</div>

<br/>

<div align="center">

🎯 **Menos desperdicio. Más economía circular.** 🌱

</div>

---

## 📖 Tabla de contenidos

<table>
<tr>
<td width="50%">

📖 **Información**
- [Acerca del proyecto](#-acerca-del-proyecto)
- [Características principales](#-características-principales)
- [Módulos del sistema](#-módulos-del-sistema)
- [Stack tecnológico](#️-stack-tecnológico)

</td>
<td width="50%">

🚀 **Guía rápida**
- [Instalación](#-instalación)
- [Servicios externos](#️-configuración-de-servicios-externos)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Convenciones](#-convenciones-de-código)

</td>
</tr>
<tr>
<td width="50%">

🤝 **Comunidad**
- [Cómo contribuir](#-cómo-contribuir)
- [Equipo](#-equipo)

</td>
<td width="50%">

📄 **Legal**
- [Licencia](#-licencia)

</td>
</tr>
</table>

---

## 📖 Acerca del proyecto

<div align="center">

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   🏭  Empresa A tiene excedentes   ──┐                          │
│                                       ├──>  🤝  EcoMatch  ──>   │
│   🏭  Empresa B los necesita      ──┘                          │
│                                                                 │
│                  ♻️  Menos desperdicio  ·  Más valor  ♻️         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

</div>

<br/>

**EcoMatch** es una plataforma web **multi-tenant** (una cuenta por empresa) que permite:

- 📦 **Publicar** materiales disponibles (excedentes, subproductos, residuos reutilizables)
- 🔎 **Buscarlos** por categoría, ubicación o empresa
- 🤝 **Abrir solicitudes** entre empresas
- 💬 **Chatear en tiempo real** para coordinar el intercambio
- 🗺️ **Visualizar todo** en un mapa interactivo

> 🔗 Repositorio: **[github.com/Team-EcoMatch/EcoMatch-Project](https://github.com/Team-EcoMatch/EcoMatch-Project)**

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## ✨ Características principales

<table>
<tr>
<td width="50%">

### 🔐 Identidad y acceso

- 🏢 **Multi-tenant con Teams** — cada empresa es un equipo aislado con sus propios datos
- 🔐 **Autenticación Laravel Fortify** — login, 2FA y recuperación de contraseña
- 👥 **Roles y permisos** — `Jefe` (aprueba, invita, elimina) y `Empleado` (opera)
- 🚫 **Bloqueo de empresas** con notificaciones persistentes

</td>
<td width="50%">

### 📦 Negocio

- 📦 **Catálogo de materiales** con subida a **Cloudinary**
- 🔎 **Buscador multi-criterio** — categoría, empresa, ubicación y estado
- 🤝 **Workflow de solicitudes**:
  `pendiente → aceptada → rechazada → completada → cancelada`
- 🗺️ **Mapa interactivo** Leaflet con marcadores filtrables por categoría

</td>
</tr>
<tr>
<td width="50%">

### ⚡ Tiempo real y UX

- 💬 **Chat bidireccional** con Laravel Reverb + Pusher + Laravel Echo
- ⚡ **SPA moderna** vía Inertia.js (sin API REST separada)
- 🧭 **Rutas type-safe** con Laravel Wayfinder

</td>
<td width="50%">

### 📄 Exportación y calidad

- 📄 **PDF** con DomPDF
- 📊 **Excel** con Maatwebsite
- 🧪 **Pint** (formatter) · **PHPStan** · **Pest**
- 🤖 **CI/CD** con GitHub Actions

</td>
</tr>
</table>

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 🧩 Módulos del sistema

El proyecto está compuesto por **9 módulos funcionales**:

| #  | Módulo | Descripción |
|----|--------|-------------|
| 1  | 📊 **Dashboard** | KPIs del team actual, accesos rápidos y resumen operativo |
| 2  | 🏢 **Perfil de Empresa** | Edición de logo, RUC, dirección, rubro y datos públicos |
| 3  | 🏷️ **Categorías** | CRUD RESTful de categorías (admin por Jefe) |
| 4  | 📦 **Mis Publicaciones** | Alta de materiales con fotos, estado y aprobación |
| 5  | 🔎 **Buscar Materiales** | Filtros combinables, debounce y partial reloads |
| 6  | 🤝 **Solicitudes** | Máquina de estados entre empresas + historial |
| 7  | 💬 **Chats** | Mensajería bidireccional en tiempo real con canales privados |
| 8  | 🗺️ **Mapa Interactivo** | Geolocalización de publicaciones con Leaflet + clustering |
| 9  | 👥 **Empleados** | Invitaciones por email, roles, permisos y desactivación |

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 🛠️ Stack tecnológico

<div align="center">

### 🎨 Frontend

<img src="https://img.shields.io/badge/Vue.js-3.5-42b883?style=for-the-badge&logo=vuedotjs&logoColor=white"/>
<img src="https://img.shields.io/badge/Inertia.js-3-6366f1?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Tailwind_CSS-4.1-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
<img src="https://img.shields.io/badge/Reka_UI-000?style=for-the-badge"/>
<img src="https://img.shields.io/badge/VueUse-41b883?style=for-the-badge"/>
<img src="https://img.shields.io/badge/lucide-FCC419?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white"/>

<br/><br/>

### ⚙️ Backend

<img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
<img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/Eloquent-ORM-FF2D20?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Laravel_Fortify-1.37-FF2D20?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Laravel_Wayfinder-FF2D20?style=for-the-badge"/>

<br/><br/>

### 💾 Datos y servicios

<img src="https://img.shields.io/badge/MySQL_Workbench-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
<img src="https://img.shields.io/badge/Laravel_Reverb-1.11-FF2D20?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Pusher-000?style=for-the-badge&logo=pusher&logoColor=white"/>
<img src="https://img.shields.io/badge/Cloudinary-3448C5?style=for-the-badge&logo=cloudinary&logoColor=white"/>
<img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white"/>
<img src="https://img.shields.io/badge/DomPDF-FF2D20?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Maatwebsite_Excel-FFC107?style=for-the-badge"/>

<br/><br/>

### 🧪 Calidad y CI/CD

<img src="https://img.shields.io/badge/Pint-formatter-FF2D20?style=for-the-badge"/>
<img src="https://img.shields.io/badge/PHPStan-análisis-777BB4?style=for-the-badge"/>
<img src="https://img.shields.io/badge/Pest-tests-FF36AB?style=for-the-badge"/>
<img src="https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=githubactions&logoColor=white"/>

</div>

<br/>

<details>
<summary>📋 Ver tabla detallada del stack</summary>

| Capa | Tecnología |
|------------------|-------------------------------------------------------------------|
| **Backend** | Laravel 13 · PHP 8.3 · Eloquent ORM |
| **Frontend** | Vue 3.5 · Inertia.js 3 · Tailwind CSS 4.1 · Reka UI · VueUse |
| **Icons** | lucide-vue-next |
| **Bundler** | Vite 8 |
| **Routing TS** | Laravel Wayfinder |
| **Auth** | Laravel Fortify 1.37 + Teams (multi-tenant) |
| **Base de datos** | MySQL Workbench |
| **Tiempo real** | Laravel Reverb 1.11 + Pusher + Laravel Echo |
| **Mapas** | Leaflet 1.9 + @vue-leaflet/vue-leaflet |
| **Archivos** | Cloudinary PHP SDK 3.1 |
| **Exportaciones** | barryvdh/laravel-dompdf 3.1 (PDF) · maatwebsite/excel 4.0 (Excel) |
| **UI feedback** | SweetAlert2 · notie · vue-sonner |
| **Calidad** | Pint (formatter) · PHPStan (análisis estático) · Pest (tests) |
| **CI/CD** | GitHub Actions |

</details>

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 🚀 Instalación

### ✅ Requisitos previos

<img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white"/>
<img src="https://img.shields.io/badge/Composer-2.x-885630?style=flat-square&logo=composer&logoColor=white"/>
<img src="https://img.shields.io/badge/Node.js-20+-339933?style=flat-square&logo=nodedotjs&logoColor=white"/>
<img src="https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white"/>

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

# 4. Base de datos (MySQL Workbench)
php artisan migrate --seed

# 5. Levantar el entorno de desarrollo
php artisan serve   # http://localhost:8000
npm run dev          # Vite (HMR) en otra terminal
```

### 👤 Usuario de prueba

Tras el `--seed`, se crean usuarios demo:

| Rol | Email | Contraseña |
|---------|--------------------------|------------|
| 👔 Jefe | `jefe@ecomatch.test` | `password` |
| 👨‍💼 Empleado | `empleado@ecomatch.test` | `password` |

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## ⚙️ Configuración de servicios externos

<details open>
<summary><b>🖼️ Cloudinary</b> — subida de imágenes</summary>

<br/>

Añade a tu `.env`:

```env
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```

</details>

<details>
<summary><b>📡 Laravel Reverb</b> — WebSockets locales</summary>

<br/>

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

</details>

<details>
<summary><b>⚡ Pusher</b> — alternativa en la nube a Reverb</summary>

<br/>

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=tu_app_id
PUSHER_APP_KEY=tu_app_key
PUSHER_APP_SECRET=tu_app_secret
PUSHER_APP_CLUSTER=mt1
```

</details>

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

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

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 🧑‍💻 Convenciones de código

<img src="https://img.shields.io/badge/Pint-formatter-FF2D20?style=flat-square"/>
<img src="https://img.shields.io/badge/PHPStan-análisis-777BB4?style=flat-square"/>
<img src="https://img.shields.io/badge/Pest-tests-FF36AB?style=flat-square"/>
<img src="https://img.shields.io/badge/Conventional_Commits-FE5196?style=flat-square&logo=conventionalcommits&logoColor=white"/>

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

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 🤝 Cómo contribuir

1. 🍴 Haz **fork** del repositorio (o crea una rama si tienes acceso directo)
2. 🌿 Crea una rama descriptiva: `git checkout -b feat/modulo-busqueda-avanzada`
3. 💻 Desarrolla siguiendo las **convenciones de código**
4. ✅ Verifica localmente: `./vendor/bin/pint && ./vendor/bin/phpstan analyse && php artisan test`
5. 📬 Abre un **Pull Request** describiendo el cambio y el módulo al que aporta

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 👥 Equipo

<div align="center">

<table>
<tr>
<td align="center" width="50%">

### 👤 Julián David Reyes Villate

<img src="https://img.shields.io/badge/Desarrollador-🛠️-22c55e?style=flat-square"/>

</td>
<td align="center" width="50%">

### 👤 Juan Sebastián Chaparro Salamanca

<img src="https://img.shields.io/badge/Desarrollador-🛠️-22c55e?style=flat-square"/>

</td>
</tr>
</table>

<br/>

**Organización:** [![Team-EcoMatch](https://img.shields.io/badge/Team--EcoMatch-GitHub-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Team-EcoMatch)

> 🎓 Proyecto educativo / académico

</div>

<p align="right"><a href="#top">⬆️ Volver arriba</a></p>

---

## 📄 Licencia

<div align="center">

[![MIT License](https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge&logo=opensourceinitiative&logoColor=white)](LICENSE)

Este proyecto está bajo licencia **MIT**.
Puedes usarlo, modificarlo y distribuirlo libremente.

</div>

---

<div align="center">

<img src="https://img.shields.io/badge/♻️_Menos_desperdicio-🌱_Más_economía_circular-22c55e?style=for-the-badge&labelColor=0f172a"/>

<br/><br/>

### Hecho con 💚 por

# Julián David Reyes Villate  ·  Juan Sebastián Chaparro Salamanca

<br/>

⭐ Si te gusta el proyecto, ¡déjanos una estrella! ⭐

<br/>

[![Star repo](https://img.shields.io/github/stars/Team-EcoMatch/EcoMatch-Project?style=social)](https://github.com/Team-EcoMatch/EcoMatch-Project)

</div>
