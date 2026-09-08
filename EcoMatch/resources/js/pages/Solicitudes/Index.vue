<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CheckCircle2, XCircle, Inbox, Send } from 'lucide-vue-next';

interface Solicitud {
    idsolicitud: number;
    idpublicaciones: number;
    idEmpresaOrigen: number;
    idEmpresaDestino: number;
    mensaje: string;
    cantidad: number;
    estado: 'Pendiente' | 'Aceptado' | 'Rechazado' | 'Completado';
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

const props = defineProps<{
    recibidas: Solicitud[];
    enviadas: Solicitud[];
   
}>();


const page = usePage();
const message = (page.props.flash as any)?.message ?? null;

const showToast = ref(false);
const toastMessage = ref('');

onMounted(() => {
    if (message) {
        toastMessage.value = message;
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
});

function actualizarEstado(id: number, nuevoEstado: string) {
    if (confirm(`¿Cambiar estado de la solicitud a "${nuevoEstado}"?`)) {
        router.put(`/solicitudes/${id}`, { estado: nuevoEstado }, {
            preserveScroll: true,
            onSuccess: (page) => {
                const msg = (page.props.flash as any)?.message ?? 'Estado actualizado.';
                toastMessage.value = msg;
                showToast.value = true;
                setTimeout(() => { showToast.value = false; }, 3000);
            }
        });
    }
}

function getEstadoBadge(estado: string) {
    const map: Record<string, string> = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-700',
        'Aceptado': 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700',
        'Rechazado': 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-700',
        'Completado': 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
    };
    return map[estado] || 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600';
}

</script>

<template>

    <Head title="Mis Solicitudes" />

    <div class="p-6 bg-background min-h-screen text-foreground">
        <!-- Toast notification -->
        <div v-if="showToast"
            class="fixed top-6 right-6 z-50 bg-card border border-border shadow-lg rounded-lg p-4 max-w-sm">
            <p class="text-sm font-medium">{{ toastMessage }}</p>
        </div>

        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">Gestión de Solicitudes</h2>

            <!-- Solicitudes recibidas -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Inbox class="w-5 h-5" />
                    Solicitudes Recibidas
                    <Badge v-if="recibidas.filter(s => s.estado === 'Pendiente').length > 0" class="ml-2">
                        {{recibidas.filter(s => s.estado === 'Pendiente').length}} pendientes
                    </Badge>
                </h3>

                <div v-if="recibidas.length === 0" class="text-muted-foreground text-sm">
                    No has recibido solicitudes.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Card v-for="sol in recibidas" :key="sol.idsolicitud"
                        class="shadow-sm hover:shadow-md transition-shadow">
                        <CardHeader class="pb-2">
                            <div class="flex justify-between items-start">
                                <CardTitle class="text-base">{{ sol.publicacion?.nombre || 'Material' }}</CardTitle>
                                <Badge :class="getEstadoBadge(sol.estado)" variant="outline">
                                    {{ sol.estado }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent>
                                                <p class="text-sm text-muted-foreground mb-2">
                                <strong>Solicitante:</strong>
                                {{ (sol.empresa_origen || sol.empresa_origen)?.nombreEmpresa || 'Empresa desconocida' }}
                            </p>

                            <p class="text-sm text-muted-foreground mb-3">
                                <strong>Mensaje:</strong> {{ sol.mensaje }}
                            </p>

                            <p class="text-xs text-muted-foreground">
                                Cantidad solicitada: {{ sol.cantidad || 0 }} {{ sol.publicacion?.unidadMedida || '' }}
                            </p>
                            <!-- Botones de acción (solo si está Pendiente) -->
                            <div v-if="sol.estado === 'Pendiente'" class="flex gap-2 mt-3">
                                <Button size="sm" @click="actualizarEstado(sol.idsolicitud, 'Aceptado')"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white">
                                    <CheckCircle2 class="w-4 h-4 mr-1" />
                                    Aceptar
                                </Button>
                                <Button size="sm" variant="destructive"
                                    @click="actualizarEstado(sol.idsolicitud, 'Rechazado')">
                                    <XCircle class="w-4 h-4 mr-1" />
                                    Rechazar
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Solicitudes enviadas -->
            <div>
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Send class="w-5 h-5" />
                    Solicitudes Enviadas
                </h3>

                <div v-if="enviadas.length === 0" class="text-muted-foreground text-sm">
                    No has enviado solicitudes.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Card v-for="sol in enviadas" :key="sol.idsolicitud"
                        class="shadow-sm hover:shadow-md transition-shadow">
                        <CardHeader class="pb-2">
                            <div class="flex justify-between items-start">
                                <CardTitle class="text-base">{{ sol.publicacion?.nombre || 'Material' }}</CardTitle>
                                <Badge :class="getEstadoBadge(sol.estado)" variant="outline">
                                    {{ sol.estado }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground mb-2">
                                Publicado por: {{ sol.publicacion?.empresa?.nombreEmpresa || 'N/A' }}
                            </p>
                            <p class="text-sm text-muted-foreground mb-3">
                                <strong>Mensaje:</strong> {{ sol.mensaje }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Cantidad solicitada: {{ sol.cantidad || 0 }} {{ sol.publicacion?.unidadMedida || '' }}
                            </p>

                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>