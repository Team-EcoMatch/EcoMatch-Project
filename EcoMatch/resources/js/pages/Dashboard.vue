<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { PackageCheck, Clock, Inbox, ArrowRight, Leaf, Users, MapPinned, Globe2, Tags, Send, Layers } from 'lucide-vue-next';

defineProps<{
    stats: {
        activas: number;
        pendientes: number;
        solicitudes: number;
        mercado: number;
        empleados: number;
        categorias: number;
        misSolicitudes: number;
        misPublicaciones: number;
    }
}>();

const page = usePage();
const userRol = page.props.auth.user.rol;
</script>

<template>
    <div class="p-6 md:p-10 bg-background min-h-screen text-foreground">
        <Head title="Panel de Control" />

        <div class="max-w-7xl mx-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Panel de Control</h1>
                    <p class="text-muted-foreground mt-1">Resumen de la actividad de tu empresa.</p>
                </div>
                <Link href="/publicaciones/create">
                    <Button class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg w-full sm:w-auto">
                        Publicar Material
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>
                </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Publicaciones Activas</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10">
                            <PackageCheck class="h-4 w-4 text-emerald-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.activas }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Materiales disponibles para intercambio</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Jefe'" class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pendientes (Jefe)</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-500/10">
                            <Clock class="h-4 w-4 text-yellow-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.pendientes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Publicaciones esperando aprobación</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Jefe'" class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Solicitudes Nuevas</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10">
                            <Inbox class="h-4 w-4 text-blue-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.solicitudes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Empresas interesadas en tus materiales</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Empresa'" class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Mercado Total</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10">
                            <Globe2 class="h-4 w-4 text-blue-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.mercado }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Materiales disponibles en la plataforma</p>
                    </CardContent>
                </Card>
            </div>

            <div v-if="userRol === 'Jefe'" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Empleados de la Empresa</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10">
                            <Users class="h-4 w-4 text-indigo-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.empleados }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Usuarios registrados en tu empresa</p>
                    </CardContent>
                </Card>

                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Categorías Creadas</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-500/10">
                            <Tags class="h-4 w-4 text-purple-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.categorias }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Clasificaciones de materiales disponibles</p>
                    </CardContent>
                </Card>
            </div>

            <div v-if="userRol === 'Empresa'" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Mis Solicitudes Enviadas</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500/10">
                            <Send class="h-4 w-4 text-sky-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.misSolicitudes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Esperando respuesta de otras empresas</p>
                    </CardContent>
                </Card>

                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Mis Publicaciones</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10">
                            <Layers class="h-4 w-4 text-indigo-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.misPublicaciones }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Total de materiales que has aportado</p>
                    </CardContent>
                </Card>

                <Card class="bg-card border-border shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Pendientes de Aprobación</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-500/10">
                            <Clock class="h-4 w-4 text-yellow-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-4xl font-bold text-foreground">{{ stats.pendientes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Tus materiales esperando al Jefe</p>
                    </CardContent>
                </Card>
            </div>

            <h2 class="text-xl font-bold mb-4">Accesos Rápidos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link href="/publicaciones" class="block">
                    <Card class="bg-card border-border shadow-sm hover:shadow-md hover:bg-accent transition-all h-full">
                        <CardContent class="p-6 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <PackageCheck class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="font-semibold">Ver Publicaciones</p>
                                <p class="text-xs text-muted-foreground">Administra tus materiales</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link href="/solicitudes" class="block">
                    <Card class="bg-card border-border shadow-sm hover:shadow-md hover:bg-accent transition-all h-full">
                        <CardContent class="p-6 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <Inbox class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="font-semibold">Bandeja de Solicitudes</p>
                                <p class="text-xs text-muted-foreground">Revisa tus intercambios</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link href="/mapa" class="block">
                    <Card class="bg-card border-border shadow-sm hover:shadow-md hover:bg-accent transition-all h-full">
                        <CardContent class="p-6 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <MapPinned class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="font-semibold">Explorar Mapa</p>
                                <p class="text-xs text-muted-foreground">Encuentra empresas cerca</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link v-if="userRol === 'Jefe'" href="/empleados" class="block">
                    <Card class="bg-card border-border shadow-sm hover:shadow-md hover:bg-accent transition-all h-full">
                        <CardContent class="p-6 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <Users class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="font-semibold">Gestionar Empleados</p>
                                <p class="text-xs text-muted-foreground">Crea y administra cuentas</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </div>
</template>