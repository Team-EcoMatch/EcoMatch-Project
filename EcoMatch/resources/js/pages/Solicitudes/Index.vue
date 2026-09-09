<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CheckCircle2, XCircle, Inbox, Send, X } from 'lucide-vue-next';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';


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
const toastType = ref<'success' | 'error'>('success');

onMounted(() => {
    if (message) {
        toastMessage.value = message;
        toastType.value = 'success';
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
});

// --- Confirmación de cambio de estado con AlertDialog estilizado (reemplaza confirm()) ---
const showConfirmDialog = ref(false);
const accionPendiente = ref<{ id: number; nuevoEstado: string } | null>(null);

function abrirConfirmacion(id: number, nuevoEstado: string) {
    accionPendiente.value = { id, nuevoEstado };
    showConfirmDialog.value = true;
}

function confirmarCambioEstado() {
    if (!accionPendiente.value) return;
    const { id, nuevoEstado } = accionPendiente.value;

    router.put(`/solicitudes/${id}`, { estado: nuevoEstado }, {
        preserveScroll: true,
        onSuccess: (page) => {
            const msg = (page.props.flash as any)?.message ?? 'Estado actualizado.';
            toastMessage.value = msg;
            toastType.value = 'success';
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 3000);
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors).flat()[0] || 'Error al actualizar el estado.';
            toastMessage.value = 'Error: ' + errorMsg;
            toastType.value = 'error';
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 5000);
            console.error('Error al actualizar:', errors);
        },
        onFinish: () => {
            showConfirmDialog.value = false;
            accionPendiente.value = null;
        }
    });
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
        <!-- Toast notification (estilo idéntico al de Publicaciones) -->
        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showToast"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                        :class="toastType === 'success' ? 'bg-green-500/10' : 'bg-red-500/10'">
                        <CheckCircle2 class="h-5 w-5"
                            :class="toastType === 'success' ? 'text-green-500' : 'text-red-500'" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-foreground">{{ toastType === 'success' ? 'Acción completada' :
                            'Error' }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ toastMessage }}</p>
                    </div>
                    <button type="button" @click="showToast = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
                <div class="h-1 bg-muted">
                    <div class="h-full" :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
                        :style="{ animation: 'toast-progress 3s linear forwards' }">
                    </div>
                </div>
            </div>
        </Transition>

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
                                {{ sol.empresa_origen?.nombreEmpresa || 'Empresa desconocida' }}
                            </p>
                            <p class="text-sm text-muted-foreground mb-3">
                                <strong>Mensaje:</strong> {{ sol.mensaje }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Cantidad solicitada: {{ sol.cantidad || 0 }} {{ sol.publicacion?.unidadMedida || '' }}
                            </p>
                            <!-- Botones de acción (solo si está Pendiente) -->
                            <div v-if="sol.estado === 'Pendiente'" class="flex gap-2 mt-3">
                                <Button size="sm" @click="abrirConfirmacion(sol.idsolicitud, 'Aceptado')"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white">
                                    <CheckCircle2 class="w-4 h-4 mr-1" />
                                    Aceptar
                                </Button>
                                <Button size="sm" variant="destructive"
                                    @click="abrirConfirmacion(sol.idsolicitud, 'Rechazado')">
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

        <!-- Confirmación estilizada (mismo estilo que "Solicitar Intercambio" en Publicaciones) -->
        <AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
            <AlertDialogContent class="bg-card border-border text-foreground">
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {{ accionPendiente?.nuevoEstado === 'Aceptado' ? 'Aceptar solicitud' : 'Rechazar solicitud' }}
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-muted-foreground">
                        ¿Estás seguro de que deseas
                        <strong>{{ accionPendiente?.nuevoEstado === 'Aceptado' ? 'aceptar' : 'rechazar' }}</strong>
                        esta solicitud de intercambio? Esta acción actualizará el estado de forma inmediata.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        Cancelar
                    </AlertDialogCancel>
                    <AlertDialogAction @click="confirmarCambioEstado" :class="accionPendiente?.nuevoEstado === 'Aceptado'
                        ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                        : 'bg-destructive hover:bg-destructive/90 text-destructive-foreground'">
                        Sí, {{ accionPendiente?.nuevoEstado === 'Aceptado' ? 'aceptar' : 'rechazar' }}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

    </div>
</template>

<style>
@keyframes toast-progress {
    from {
        width: 100%;
    }

    to {
        width: 0%;
    }
}
</style>
