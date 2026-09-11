<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { History, ArrowRight } from 'lucide-vue-next';

interface Solicitud {
    idsolicitud: number;
    mensaje: string;
    estado: string;
    created_at: string;
    publicacion: {
        nombre: string;
        cantidad: number;
        unidadMedida: string;
        empresa: { nombreEmpresa: string };
    };
    empresa_origen?: { nombreEmpresa: string };
    empresa_destino?: { nombreEmpresa: string };
}

defineProps<{
    historial: Solicitud[];
}>();

function getEstadoBadge(estado: string) {
    const map: Record<string, string> = {
        'Aceptado': 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700',
        'Rechazado': 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-700',
        'Completado': 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
    };
    return map[estado] || 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600';
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">
        <Head title="Historial de Intercambios" />

        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <History class="w-6 h-6 text-primary" />
                Historial de Intercambios
            </h2>

            <div v-if="historial.length === 0" class="text-muted-foreground text-sm text-center py-12 border border-dashed rounded-lg">
                No has realizado ni recibido intercambios todavía.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="sol in historial" :key="sol.idsolicitud" class="shadow-sm hover:shadow-md transition-shadow">
                    <CardHeader class="pb-2">
                        <div class="flex justify-between items-start">
                            <CardTitle class="text-base">{{ sol.publicacion?.nombre || 'Material' }}</CardTitle>
                            <Badge :class="getEstadoBadge(sol.estado)" variant="outline">
                                {{ sol.estado }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between text-sm font-medium mb-3 bg-muted/30 p-2 rounded-md">
                            <span>{{ sol.empresa_origen?.nombreEmpresa || 'N/A' }}</span>
                            <ArrowRight class="w-4 h-4 text-muted-foreground mx-2" />
                            <span>{{ sol.empresa_destino?.nombreEmpresa || 'N/A' }}</span>
                        </div>
                        
                        <p class="text-sm text-muted-foreground mb-2">
                            <strong>Cantidad:</strong> {{ sol.publicacion?.cantidad || 0 }} {{ sol.publicacion?.unidadMedida || '' }}
                        </p>
                        <p class="text-sm text-muted-foreground mb-3">
                            <strong>Mensaje:</strong> {{ sol.mensaje }}
                        </p>
                        <p class="text-xs text-muted-foreground border-t pt-2 mt-2">
                            Fecha: {{ formatDate(sol.created_at) }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>