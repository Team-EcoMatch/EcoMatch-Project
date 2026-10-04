<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { History, ArrowRight, Search, CheckCircle2, XCircle, ArrowLeftRight, PackageCheck, X } from 'lucide-vue-next';

interface Solicitud {
    idsolicitud: number;
    mensaje: string;
    estado: string;
    created_at: string;
    cantidad: number;
    publicacion: {
        nombre: string;
        cantidad: number;
        unidadMedida: string;
        empresa: { nombreEmpresa: string };
    };
    empresa_origen?: { nombreEmpresa: string };
    empresa_destino?: { nombreEmpresa: string };
}

const props = defineProps<{
    historial: Solicitud[];
}>();

const searchQuery = ref('');
const statusFilter = ref('all');

const stats = computed(() => {
    const completados = props.historial.filter(s => s.estado === 'Completado');
    return {
        completados: completados.length,
        rechazados: props.historial.filter(s => s.estado === 'Rechazado').length,
        cancelados: props.historial.filter(s => s.estado === 'Cancelada' || s.estado === 'Cancelado').length,
        total: props.historial.length,
        kgRecuperados: completados.reduce((sum, s) => sum + Number(s.cantidad || s.publicacion?.cantidad || 0), 0),
    };
});

const filteredHistorial = computed(() => {
    let list = props.historial;
    if (statusFilter.value !== 'all') {
        list = list.filter(s => s.estado === statusFilter.value);
    }
    const query = searchQuery.value.toLowerCase().trim();
    if (query) {
        list = list.filter(s => {
            const material = (s.publicacion?.nombre || '').toLowerCase();
            const origen = (s.empresa_origen?.nombreEmpresa || '').toLowerCase();
            const destino = (s.empresa_destino?.nombreEmpresa || '').toLowerCase();
            return material.includes(query) || origen.includes(query) || destino.includes(query);
        });
    }
    return list;
});

const statusFilters = [
    { value: 'all', label: 'Todos' },
    { value: 'Completado', label: 'Completados' },
    { value: 'Rechazado', label: 'Rechazados' },
    { value: 'Cancelada', label: 'Cancelados' },
];

function getEstadoBadge(estado: string) {
    const map: Record<string, string> = {
        'Aceptado': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'Rechazado': 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'Completado': 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        'Cancelada': 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        'Cancelado': 'bg-gray-500/10 text-gray-500 border-gray-500/20',
    };
    return map[estado] || 'bg-gray-500/10 text-gray-500 border-gray-500/20';
}

function getAvatarColor(nombre: string): { gradient: string; text: string } {
    const colors = [
        { gradient: 'from-blue-500/20 to-blue-500/5 border-blue-500/20', text: 'text-blue-500' },
        { gradient: 'from-emerald-500/20 to-emerald-500/5 border-emerald-500/20', text: 'text-emerald-500' },
        { gradient: 'from-amber-500/20 to-amber-500/5 border-amber-500/20', text: 'text-amber-500' },
        { gradient: 'from-purple-500/20 to-purple-500/5 border-purple-500/20', text: 'text-purple-500' },
        { gradient: 'from-pink-500/20 to-pink-500/5 border-pink-500/20', text: 'text-pink-500' },
        { gradient: 'from-indigo-500/20 to-indigo-500/5 border-indigo-500/20', text: 'text-indigo-500' },
    ];
    const hash = nombre.charCodeAt(0) % colors.length;
    return colors[hash];
}

function timeAgo(date: string): string {
    const now = new Date();
    const past = new Date(date);
    const diff = now.getTime() - past.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    if (days > 30) return past.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
    if (days > 0) return `Hace ${days}d`;
    if (hours > 0) return `Hace ${hours}h`;
    return 'Hoy';
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Historial de Intercambios" />

        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <History class="w-6 h-6 text-primary" />
                    Historial de Intercambios
                </h2>
                <p class="text-sm text-muted-foreground mt-1">Registro de todos tus intercambios</p>
            </div>

            <!-- 📊 STATS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <Card
                    class="relative overflow-hidden border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 shrink-0">
                            <ArrowLeftRight class="h-5 w-5 text-blue-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.completados }}</p>
                            <p class="text-xs text-muted-foreground">Completados</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-rose-500/20 bg-gradient-to-br from-rose-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-rose-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/15 shrink-0">
                            <XCircle class="h-5 w-5 text-rose-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.rechazados }}</p>
                            <p class="text-xs text-muted-foreground">Rechazados</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-gray-500/20 bg-gradient-to-br from-gray-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gray-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-500/15 shrink-0">
                            <X class="h-5 w-5 text-gray-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.cancelados }}</p>
                            <p class="text-xs text-muted-foreground">Cancelados</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                            <PackageCheck class="h-5 w-5 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.kgRecuperados.toLocaleString('es') }}
                            </p>
                            <p class="text-xs text-muted-foreground">Kg recuperados</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- 🔍 FILTROS -->
            <div v-if="historial.length > 0" class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                    <input v-model="searchQuery" type="text" placeholder="Buscar por empresa o material..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-input bg-background text-sm text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary outline-none" />
                </div>
                <div class="flex flex-wrap gap-2">
                    <button v-for="filter in statusFilters" :key="filter.value" @click="statusFilter = filter.value"
                        :class="['px-3', 'py-1.5', 'text-xs', 'font-medium', 'rounded-full', 'transition-colors',
                            statusFilter === filter.value ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground hover:bg-accent']">
                        {{ filter.label }}
                    </button>
                </div>
            </div>

            <!-- 📋 CARDS -->
            <div v-if="filteredHistorial.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="sol in filteredHistorial" :key="sol.idsolicitud"
                    class="hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <CardContent class="p-5">
                        <div class="flex justify-between items-start gap-2 mb-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-foreground truncate">{{ sol.publicacion?.nombre ||
                                    'Material' }}</h3>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ timeAgo(sol.created_at) }}</p>
                            </div>
                            <span
                                :class="['inline-flex', 'items-center', 'px-2.5', 'py-1', 'rounded-full', 'text-xs', 'font-semibold', 'border', getEstadoBadge(sol.estado)]">
                                {{ sol.estado }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2 mb-4 p-3 rounded-lg bg-muted/30">
                            <div class="flex items-center gap-2 min-w-0">
                                <div
                                    :class="['flex', 'h-8', 'w-8', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(sol.empresa_origen?.nombreEmpresa || 'E').gradient]">
                                    <span
                                        :class="['text-xs', 'font-bold', getAvatarColor(sol.empresa_origen?.nombreEmpresa || 'E').text]">
                                        {{ (sol.empresa_origen?.nombreEmpresa || 'E').charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium truncate">{{ sol.empresa_origen?.nombreEmpresa || 'N/A'
                                    }}</span>
                            </div>
                            <ArrowRight class="w-4 h-4 text-muted-foreground shrink-0" />
                            <div class="flex items-center gap-2 min-w-0">
                                <div
                                    :class="['flex', 'h-8', 'w-8', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(sol.empresa_destino?.nombreEmpresa || 'E').gradient]">
                                    <span
                                        :class="['text-xs', 'font-bold', getAvatarColor(sol.empresa_destino?.nombreEmpresa || 'E').text]">
                                        {{ (sol.empresa_destino?.nombreEmpresa || 'E').charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium truncate">{{ sol.empresa_destino?.nombreEmpresa ||
                                    'N/A' }}</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-sm">
                            <p class="text-muted-foreground">
                                Cantidad: <strong class="text-foreground">{{ sol.cantidad || sol.publicacion?.cantidad
                                    || 0 }} {{ sol.publicacion?.unidadMedida || '' }}</strong>
                            </p>
                            <p class="text-muted-foreground line-clamp-2">
                                <strong>Mensaje:</strong> {{ sol.mensaje }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else-if="historial.length > 0 && searchQuery" class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <Search class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p class="text-lg">No se encontraron intercambios</p>
                    <p class="text-sm mt-2">Prueba con otro término de búsqueda</p>
                </CardContent>
            </Card>

            <Card v-else-if="historial.length > 0 && statusFilter !== 'all'" class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <PackageCheck class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p class="text-lg">No hay intercambios con este estado</p>
                    <p class="text-sm mt-2">Prueba con otro filtro</p>
                </CardContent>
            </Card>

            <Card v-else class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mx-auto mb-4">
                        <History class="w-8 h-8 text-primary/50" />
                    </div>
                    <p class="text-lg font-medium">No tienes intercambios aún</p>
                    <p class="text-sm mt-1">Cuando completes un intercambio, aparecerá aquí</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>