<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    PackageCheck, Clock, Inbox, ArrowRight, Leaf, Users, MapPinned,
    Globe2, Tags, Send, Layers, TrendingUp, Calendar
} from 'lucide-vue-next';
import { Bar, Line } from 'vue-chartjs';
import {
    Chart as ChartJS, CategoryScale, LinearScale, BarElement,
    PointElement, LineElement, Title, Tooltip, Legend, Filler
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps<{
    stats: {
        activas: number;
        pendientes: number;
        solicitudes: number;
        mercado: number;
        empleados: number;
        categorias: number;
        misSolicitudes: number;
        misPublicaciones: number;
    };
    charts: {
        categorias: { labels: string[]; data: number[]; };
        dias: { labels: string[]; data: number[]; };
    };
}>();

const page = usePage();
const userRol = page.props.auth.user.rol;
const companyName = page.props.auth.user.nombreEmpresa || 'Empresa';
const userName = page.props.auth.user.name || 'Usuario';

const animatedStats = reactive({
    activas: 0, pendientes: 0, solicitudes: 0, mercado: 0,
    empleados: 0, categorias: 0, misSolicitudes: 0, misPublicaciones: 0,
});

const fechaActual = new Date().toLocaleDateString('es-ES', {
    weekday: 'long', day: 'numeric', month: 'long',
});

const totalSemana = computed(() => props.charts.dias.data.reduce((a: number, b: number) => a + b, 0));

onMounted(() => {
    const duration = 1200;
    const start = performance.now();
    const targets = { ...props.stats };
    const step = (now: number) => {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        Object.keys(targets).forEach(key => {
            (animatedStats as any)[key] = Math.round((targets as any)[key] * eased);
        });
        if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
});

const barChartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94A3B8' } },
        x: { grid: { display: false }, ticks: { color: '#94A3B8' } }
    }
};

const lineChartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94A3B8' } },
        x: { grid: { display: false }, ticks: { color: '#94A3B8' } }
    }
};
</script>

<template>
    <div class="p-6 md:p-10 bg-background min-h-screen text-foreground">

        <Head title="Panel de Control" />

        <div class="max-w-7xl mx-auto">

            <div
                class="relative overflow-hidden rounded-2xl mb-8 border border-border bg-gradient-to-br from-primary/10 via-primary/5 to-transparent">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/10 rounded-full blur-[60px]"></div>
                <div class="relative p-6 md:p-8 flex flex-col md:flex-row justify-between items-start gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-sm text-primary mb-2">
                            <Calendar class="w-4 h-4" />
                            <span class="capitalize">{{ fechaActual }}</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            Hola, <span class="text-primary">{{ userName }}</span> 👋
                        </h1>
                        <p class="text-muted-foreground mt-1">
                            Bienvenido a <strong>{{ companyName }}</strong>. Este es el resumen de hoy.
                        </p>
                    </div>
                    <Link href="/publicaciones/create">
                        <Button
                            class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg w-full md:w-auto">
                            <ArrowRight class="mr-2 h-4 w-4" />
                            Publicar Material
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Publicaciones Activas</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/15">
                            <PackageCheck class="h-4 w-4 text-emerald-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.activas }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Materiales disponibles</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Jefe'"
                    class="relative overflow-hidden border-amber-500/20 bg-gradient-to-br from-amber-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Pendientes</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15">
                            <Clock class="h-4 w-4 text-amber-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.pendientes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Esperando aprobación</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Jefe'"
                    class="relative overflow-hidden border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Solicitudes Nuevas</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/15">
                            <Inbox class="h-4 w-4 text-blue-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.solicitudes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Empresas interesadas</p>
                    </CardContent>
                </Card>

                <Card v-if="userRol === 'Empresa'"
                    class="relative overflow-hidden border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Mercado Total</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/15">
                            <Globe2 class="h-4 w-4 text-blue-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.mercado }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Materiales en la plataforma</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/5 to-transparent">
                    <CardContent class="p-6 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500/15 shrink-0">
                            <Leaf class="h-7 w-7 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-foreground tabular-nums">{{ totalSemana }}</p>
                            <p class="text-sm text-muted-foreground">Publicaciones esta semana</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/5 to-transparent">
                    <CardContent class="p-6 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/15 shrink-0">
                            <TrendingUp class="h-7 w-7 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-foreground tabular-nums">{{ animatedStats.misPublicaciones
                                }}</p>
                            <p class="text-sm text-muted-foreground">Total de tus publicaciones</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <Card class="border-border shadow-sm hover:shadow-md transition-all">
                    <CardHeader>
                        <CardTitle class="text-lg">Materiales por Categoría</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-72 w-full">
                            <Bar :data="{
                                labels: charts.categorias.labels,
                                datasets: [{ label: 'Materiales', data: charts.categorias.data, backgroundColor: '#10B981', borderRadius: 6, maxBarThickness: 40 }]
                            }" :options="barChartOptions" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-border shadow-sm hover:shadow-md transition-all">
                    <CardHeader>
                        <CardTitle class="text-lg">Publicaciones (Últimos 7 días)</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="h-72 w-full">
                            <Line :data="{
                                labels: charts.dias.labels,
                                datasets: [{ label: 'Publicaciones', data: charts.dias.data, borderColor: '#3B82F6', backgroundColor: 'rgba(59, 130, 246, 0.1)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#3B82F6' }]
                            }" :options="lineChartOptions" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="userRol === 'Jefe'" class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-8">
                <Card
                    class="relative overflow-hidden border-indigo-500/20 bg-gradient-to-br from-indigo-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Empleados</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/15">
                            <Users class="h-4 w-4 text-indigo-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.empleados }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Usuarios en tu empresa</p>
                    </CardContent>
                </Card>

                <Card
                    class="relative overflow-hidden border-purple-500/20 bg-gradient-to-br from-purple-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-purple-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Categorías</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-500/15">
                            <Tags class="h-4 w-4 text-purple-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.categorias }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Clasificaciones creadas</p>
                    </CardContent>
                </Card>
            </div>

            <div v-if="userRol === 'Empresa'" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <Card
                    class="relative overflow-hidden border-sky-500/20 bg-gradient-to-br from-sky-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-sky-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Mis Solicitudes</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500/15">
                            <Send class="h-4 w-4 text-sky-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.misSolicitudes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Esperando respuesta</p>
                    </CardContent>
                </Card>

                <Card
                    class="relative overflow-hidden border-indigo-500/20 bg-gradient-to-br from-indigo-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Mis Publicaciones</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/15">
                            <Layers class="h-4 w-4 text-indigo-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.misPublicaciones }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Materiales aportados</p>
                    </CardContent>
                </Card>

                <Card
                    class="relative overflow-hidden border-amber-500/20 bg-gradient-to-br from-amber-500/10 to-transparent hover:shadow-lg transition-all">
                    <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs font-medium text-muted-foreground">Pendientes</CardTitle>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15">
                            <Clock class="h-4 w-4 text-amber-500" />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl md:text-4xl font-bold text-foreground tabular-nums">{{
                            animatedStats.pendientes }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Esperando al Jefe</p>
                    </CardContent>
                </Card>
            </div>

            <h2 class="text-xl font-bold mb-4">Accesos Rápidos</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <Link href="/publicaciones" class="block">
                    <Card
                        class="border-border shadow-sm hover:shadow-md hover:border-primary/40 hover:bg-accent transition-all h-full">
                        <CardContent class="p-5 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                                <PackageCheck class="h-5 w-5 text-emerald-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Publicaciones</p>
                                <p class="text-xs text-muted-foreground">Administra materiales</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link href="/solicitudes" class="block">
                    <Card
                        class="border-border shadow-sm hover:shadow-md hover:border-primary/40 hover:bg-accent transition-all h-full">
                        <CardContent class="p-5 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 shrink-0">
                                <Inbox class="h-5 w-5 text-blue-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Solicitudes</p>
                                <p class="text-xs text-muted-foreground">Revisa intercambios</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link href="/mapa" class="block">
                    <Card
                        class="border-border shadow-sm hover:shadow-md hover:border-primary/40 hover:bg-accent transition-all h-full">
                        <CardContent class="p-5 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/15 shrink-0">
                                <MapPinned class="h-5 w-5 text-purple-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Mapa</p>
                                <p class="text-xs text-muted-foreground">Empresas cercanas</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link v-if="userRol === 'Jefe'" href="/empleados" class="block">
                    <Card
                        class="border-border shadow-sm hover:shadow-md hover:border-primary/40 hover:bg-accent transition-all h-full">
                        <CardContent class="p-5 flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/15 shrink-0">
                                <Users class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Empleados</p>
                                <p class="text-xs text-muted-foreground">Gestiona cuentas</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link v-else href="/buscar" class="block">
                    <Card
                        class="border-border shadow-sm hover:shadow-md hover:border-primary/40 hover:bg-accent transition-all h-full">
                        <CardContent class="p-5 flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 shrink-0">
                                <Globe2 class="h-5 w-5 text-sky-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm">Buscar</p>
                                <p class="text-xs text-muted-foreground">Encuentra materiales</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </div>
</template>