<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { onMounted } from 'vue';
import {
    BarChart3, TrendingUp, Building2, FileText,
    ArrowLeftRight, Users, PackageCheck, Calendar,
    Filter, Award, Layers, FileSpreadsheet
} from 'lucide-vue-next';
import { Bar, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    ArcElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, ArcElement, CategoryScale, LinearScale);

const props = defineProps<{
    rol: string;
    esAdmin: boolean;
    esJefe: boolean;
    esEmpleado: boolean;
    indicadores: any;
    publicacionesPorEstado: Record<string, number>;
    solicitudesPorEstado: Record<string, number>;
    publicacionesPorMes: Record<string, number>;
    solicitudesPorMes: Record<string, number>;
    materialesIntercambiados: any[];
    topEmpresas: any[];
    topMateriales: any[];
    porCategoria: any[];
    periodo: any;
    filtros: any;
    desempenoEmpleados: any[];
}>();

const desde = ref(props.filtros?.desde || '');
const hasta = ref(props.filtros?.hasta || '');

function aplicarFiltros() {
    router.get('/reportes', { desde: desde.value, hasta: hasta.value }, {
        preserveScroll: true,
        preserveState: true,
    });
}

function limpiarFiltros() {
    desde.value = '';
    hasta.value = '';
    router.get('/reportes', {}, { preserveScroll: true, preserveState: true });
}

const coloresEstado: Record<string, string> = {
    'Disponible': '#34d399',
    'Pendiente': '#fbbf24',
    'Agotado': '#fb923c',
    'Reservado': '#60a5fa',
    'Intercambiado': '#a78bfa',
    'Inactivo': '#9ca3af',
    'Rechazado': '#f87171',
};

const chartPublicacionesEstado = computed(() => ({
    labels: Object.keys(props.publicacionesPorEstado),
    datasets: [{
        data: Object.values(props.publicacionesPorEstado),
        backgroundColor: Object.keys(props.publicacionesPorEstado).map(
            (e) => coloresEstado[e] || '#9ca3af'
        ),
    }],
}));

const chartSolicitudesEstado = computed(() => ({
    labels: Object.keys(props.solicitudesPorEstado),
    datasets: [{
        data: Object.values(props.solicitudesPorEstado),
        backgroundColor: ['#fbbf24', '#34d399', '#f87171', '#60a5fa'],
    }],
}));

const chartTendencia = computed(() => {
    const meses = Array.from(new Set([
        ...Object.keys(props.publicacionesPorMes),
        ...Object.keys(props.solicitudesPorMes),
    ])).sort();

    return {
        labels: meses,
        datasets: [
            {
                label: 'Publicaciones',
                data: meses.map(m => props.publicacionesPorMes[m] || 0),
                backgroundColor: '#34d399',
                maxBarThickness: 24,
            },
            {
                label: 'Solicitudes',
                data: meses.map(m => props.solicitudesPorMes[m] || 0),
                backgroundColor: '#60a5fa',
                maxBarThickness: 24,
            },
        ],
    };
});

const chartCategorias = computed(() => ({
    labels: props.porCategoria.map((c: any) => c.nombre),
    datasets: [{
        label: 'Publicaciones',
        data: props.porCategoria.map((c: any) => c.total),
        backgroundColor: '#a78bfa',
        maxBarThickness: 24,
    }],
}));

const isDark = ref(false);
const textColor = ref('#111827');
const gridColor = ref('rgba(0,0,0,0.1)');
const tickColor = ref('#6b7280');

function actualizarColores() {
    isDark.value = document.documentElement.classList.contains('dark');
    textColor.value = isDark.value ? '#f9fafb' : '#111827';
    gridColor.value = isDark.value ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
    tickColor.value = isDark.value ? '#d1d5db' : '#6b7280';
}

onMounted(() => {
    actualizarColores();
    const observer = new MutationObserver(actualizarColores);
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
            labels: {
                color: textColor.value,
                font: { size: 12 },
                padding: 12,
            },
        },
        tooltip: {
            backgroundColor: isDark.value ? '#1f2937' : '#ffffff',
            titleColor: textColor.value,
            bodyColor: textColor.value,
            borderColor: isDark.value ? '#374151' : '#e5e7eb',
            borderWidth: 1,
        },
    },
    scales: {
        x: {
            ticks: { color: tickColor.value },
            grid: { color: gridColor.value },
            border: { color: gridColor.value },
        },
        y: {
            ticks: { color: tickColor.value },
            grid: { color: gridColor.value },
            border: { color: gridColor.value },
        },
    },
}));
</script>

<template>

    <Head :title="esAdmin ? 'Reportes Globales' : 'Reportes de mi Empresa'" />

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-foreground flex items-center gap-2">
                    <BarChart3 class="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                    {{ esAdmin ? 'Reportes e Indicadores Globales' : 'Reportes de mi Empresa' }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    {{ esAdmin ? 'Estadísticas globales' : 'Estadísticas de tu empresa' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a :href="`/reportes/export/pdf?desde=${desde}&hasta=${hasta}`" target="_blank">
                    <Button variant="outline" class="h-10">
                        <FileText class="w-4 h-4 mr-2" />
                        <span class="text-xs sm:text-sm">PDF</span>
                    </Button>
                </a>
                <a :href="`/reportes/export/excel?desde=${desde}&hasta=${hasta}`">
                    <Button variant="outline" class="h-10">
                        <FileSpreadsheet class="w-4 h-4 mr-2" />
                        <span class="text-xs sm:text-sm">Excel</span>
                    </Button>
                </a>
            </div>
        </div>

        <Card class="mb-6">
            <CardContent class="p-4">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <Label class="text-xs text-muted-foreground dark:text-zinc-200">Desde</Label>
                        <Input v-model="desde" type="date" class="mt-1" />
                    </div>
                    <div class="flex-1 w-full">
                        <Label class="text-xs text-muted-foreground dark:text-zinc-200">Hasta</Label>
                        <Input v-model="hasta" type="date" class="mt-1" />
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <Button @click="aplicarFiltros" class="bg-primary text-primary-foreground flex-1 md:flex-none">
                            <Filter class="w-4 h-4 mr-2" />
                            Filtrar
                        </Button>
                        <Button variant="outline" @click="limpiarFiltros" class="flex-1 md:flex-none">
                            Limpiar
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card v-if="esJefe" class="mb-6">
            <CardHeader>
                <CardTitle class="text-base flex items-center gap-2">
                    <Users class="w-4 h-4 text-blue-500" />
                    Desempeño por Empleado
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="desempenoEmpleados.length === 0" class="text-sm text-muted-foreground text-center py-4">
                    No hay empleados registrados.
                </div>

                <div v-else class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border">
                                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Empleado</th>
                                <th class="text-right py-3 px-4 font-medium text-muted-foreground">Publicaciones</th>
                                <th class="text-right py-3 px-4 font-medium text-muted-foreground">Solicitudes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="emp in desempenoEmpleados" :key="emp.id"
                                class="border-b border-border/50 hover:bg-muted/30 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs">
                                            {{ emp.nombre.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-foreground dark:text-white">{{ emp.nombre }}
                                            </div>
                                            <div class="text-xs text-muted-foreground">{{ emp.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold text-sm">
                                        {{ emp.publicaciones }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span
                                        class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                        {{ emp.solicitudes }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="desempenoEmpleados.length > 0" class="md:hidden space-y-3">
                    <div v-for="emp in desempenoEmpleados" :key="emp.id"
                        class="border border-border rounded-xl p-4 bg-muted/10">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold">
                                {{ emp.nombre.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-foreground dark:text-white truncate">{{ emp.nombre }}</div>
                                <div class="text-xs text-muted-foreground truncate">{{ emp.email }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-emerald-500/10 rounded-lg p-3 text-center">
                                <p class="text-xs text-muted-foreground mb-1">Publicaciones</p>
                                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ emp.publicaciones }}</p>
                            </div>
                            <div class="bg-blue-500/10 rounded-lg p-3 text-center">
                                <p class="text-xs text-muted-foreground mb-1">Solicitudes</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ emp.solicitudes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div
            :class="esAdmin ? 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6' : 'grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6'">
            <Card v-if="esAdmin">
                <CardContent class="p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <Building2 class="w-4 h-4 text-blue-500" />
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Empresas</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-foreground dark:text-white">
                        {{ indicadores.totalEmpresas }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <Users class="w-4 h-4 text-purple-500" />
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">
                            {{ esAdmin ? 'Usuarios' : 'Usuarios' }}
                        </p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-foreground dark:text-white">
                        {{ indicadores.totalUsuarios }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <FileText class="w-4 h-4 text-emerald-500" />
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Publicaciones</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-foreground dark:text-white">
                        {{ indicadores.totalPublicaciones }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <ArrowLeftRight class="w-4 h-4 text-amber-500" />
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Solicitudes</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-foreground dark:text-white">
                        {{ indicadores.totalSolicitudes }}</p>
                </CardContent>
            </Card>
            <Card class="bg-emerald-50 dark:bg-emerald-950/30">
                <CardContent class="p-3 sm:p-4">
                    <div class="flex items-center gap-2 mb-1">
                        <PackageCheck class="w-4 h-4 text-emerald-600" />
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Tasa Aceptación</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                        {{ indicadores.tasaAceptacion }}%
                    </p>
                </CardContent>
            </Card>
        </div>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="text-base flex items-center gap-2">
                    <Calendar class="w-4 h-4" />
                    Actividad en el Periodo Seleccionado
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-muted/30 p-3 rounded-lg">
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Publicaciones nuevas</p>
                        <p class="text-lg sm:text-xl font-bold text-foreground dark:text-white">
                            {{ periodo.publicaciones }}</p>
                    </div>
                    <div class="bg-muted/30 p-3 rounded-lg">
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Solicitudes nuevas</p>
                        <p class="text-lg sm:text-xl font-bold text-foreground dark:text-white">
                            {{ periodo.solicitudes }}</p>
                    </div>
                    <div v-if="esAdmin" class="bg-muted/30 p-3 rounded-lg">
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Empresas nuevas</p>
                        <p class="text-lg sm:text-xl font-bold text-foreground dark:text-white">
                            {{ periodo.empresas_nuevas }}</p>
                    </div>
                    <div class="bg-muted/30 p-3 rounded-lg">
                        <p class="text-xs text-muted-foreground dark:text-zinc-300">Usuarios nuevos</p>
                        <p class="text-lg sm:text-xl font-bold text-foreground dark:text-white">
                            {{ periodo.usuarios_nuevos }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="text-base flex items-center gap-2">
                    <TrendingUp class="w-4 h-4 text-emerald-500" />
                    Impacto Ambiental - Materiales Intercambiados
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="materialesIntercambiados.length === 0"
                    class="text-sm text-muted-foreground dark:text-zinc-400 text-center py-4">
                    Aún no hay intercambios completados.
                </div>
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                    <div v-for="mat in materialesIntercambiados" :key="mat.unidadMedida"
                        class="border border-border rounded-lg p-4">
                        <p class="text-xs font-semibold text-muted-foreground dark:text-zinc-200">
                            {{ mat.unidadMedida }}</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            {{ Number(mat.total_cantidad).toLocaleString() }}
                        </p>
                        <p class="text-xs text-muted-foreground dark:text-zinc-400 mt-1">
                            {{ mat.total_intercambios }} intercambio(s)
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Publicaciones por Estado</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="h-56 sm:h-64">
                        <Doughnut :data="chartPublicacionesEstado" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Solicitudes por Estado</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="h-56 sm:h-64">
                        <Doughnut :data="chartSolicitudesEstado" :options="chartOptions" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="text-base">Tendencia Mensual (últimos 12 meses)</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="h-64 sm:h-72">
                    <Bar :data="chartTendencia" :options="chartOptions" />
                </div>
            </CardContent>
        </Card>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="text-base flex items-center gap-2">
                    <Layers class="w-4 h-4" />
                    Top 10 Categorías más Publicadas
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="h-64 sm:h-72">
                    <Bar :data="chartCategorias" :options="chartOptions" />
                </div>
            </CardContent>
        </Card>

        <div :class="esAdmin ? 'grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6' : 'grid grid-cols-1 gap-4 sm:gap-6'">
            <Card v-if="esAdmin">
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <Award class="w-4 h-4 text-amber-500" />
                        Top 5 Empresas con más Publicaciones
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="topEmpresas.length === 0"
                        class="text-sm text-muted-foreground dark:text-zinc-400 text-center py-4">
                        No hay datos.
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="(emp, index) in topEmpresas" :key="emp.idempresa" class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary font-bold text-sm shrink-0">
                                {{ index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate text-foreground dark:text-white">
                                    {{ emp.nombreEmpresa }}</p>
                            </div>
                            <Badge variant="outline" class="shrink-0">
                                {{ emp.publicaciones_count }} pub.
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <PackageCheck class="w-4 h-4 text-emerald-500" />
                        Top 5 Materiales más Publicados
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="topMateriales.length === 0"
                        class="text-sm text-muted-foreground dark:text-zinc-400 text-center py-4">
                        No hay datos.
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="(mat, index) in topMateriales" :key="mat.nombre" class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600 font-bold text-sm shrink-0">
                                {{ index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate text-foreground dark:text-white">
                                    {{ mat.nombre }}</p>
                            </div>
                            <Badge variant="outline" class="shrink-0">
                                {{ mat.total }} pub.
                            </Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>