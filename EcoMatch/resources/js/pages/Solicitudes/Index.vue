<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CheckCircle2, XCircle, Inbox, Send, X, Clock, PackageCheck, ArrowLeftRight } from 'lucide-vue-next';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
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

const activeTab = ref<'recibidas' | 'enviadas'>('recibidas');
const statusFilter = ref<string>('all');

onMounted(() => {
    if (message) {
        toastMessage.value = message;
        toastType.value = 'success';
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
});

const stats = computed(() => {
    const all = [...props.recibidas, ...props.enviadas];
    return {
        pendientes: all.filter(s => s.estado === 'Pendiente').length,
        aceptadas: all.filter(s => s.estado === 'Aceptado').length,
        completadas: all.filter(s => s.estado === 'Completado').length,
        total: all.length,
    };
});

const currentList = computed(() => activeTab.value === 'recibidas' ? props.recibidas : props.enviadas);

const filteredList = computed(() => {
    if (statusFilter.value === 'all') return currentList.value;
    return currentList.value.filter(s => s.estado === statusFilter.value);
});

const pendientesCount = computed(() => props.recibidas.filter(s => s.estado === 'Pendiente').length);

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
        },
        onFinish: () => {
            showConfirmDialog.value = false;
            accionPendiente.value = null;
        }
    });
}

function getEstadoBadge(estado: string) {
    const map: Record<string, string> = {
        'Pendiente': 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'Aceptado': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'Rechazado': 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'Completado': 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
    };
    return map[estado] || 'bg-gray-500/10 text-gray-500 border-gray-500/20';
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
    return 'Ahora';
}

const statusFilters = [
    { value: 'all', label: 'Todos' },
    { value: 'Pendiente', label: 'Pendientes' },
    { value: 'Aceptado', label: 'Aceptadas' },
    { value: 'Completado', label: 'Completadas' },
    { value: 'Rechazado', label: 'Rechazadas' },
];
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Mis Solicitudes" />

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
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">Gestión de Solicitudes</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <Card
                    class="relative overflow-hidden border-amber-500/20 bg-gradient-to-br from-amber-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 shrink-0">
                            <Clock class="h-5 w-5 text-amber-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.pendientes }}</p>
                            <p class="text-xs text-muted-foreground">Pendientes</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                            <CheckCircle2 class="h-5 w-5 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.aceptadas }}</p>
                            <p class="text-xs text-muted-foreground">Aceptadas</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 shrink-0">
                            <ArrowLeftRight class="h-5 w-5 text-blue-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.completadas }}</p>
                            <p class="text-xs text-muted-foreground">Completadas</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/15 shrink-0">
                            <PackageCheck class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.total }}</p>
                            <p class="text-xs text-muted-foreground">Total</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex gap-1 mb-4 border-b border-border">
                <button @click="activeTab = 'recibidas'"
                    :class="['flex', 'items-center', 'gap-2', 'px-4', 'py-3', 'text-sm', 'font-medium', 'transition-colors', 'border-b-2', '-mb-px',
                        activeTab === 'recibidas' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground']">
                    <Inbox class="w-4 h-4" />
                    Recibidas
                    <span v-if="pendientesCount > 0"
                        class="ml-1 px-2 py-0.5 text-xs rounded-full bg-amber-500 text-white">{{ pendientesCount
                        }}</span>
                </button>
                <button @click="activeTab = 'enviadas'"
                    :class="['flex', 'items-center', 'gap-2', 'px-4', 'py-3', 'text-sm', 'font-medium', 'transition-colors', 'border-b-2', '-mb-px',
                        activeTab === 'enviadas' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground']">
                    <Send class="w-4 h-4" />
                    Enviadas
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-muted text-muted-foreground">{{
                        enviadas.length }}</span>
                </button>
            </div>

            <div class="flex flex-wrap gap-2 mb-6">
                <button v-for="filter in statusFilters" :key="filter.value" @click="statusFilter = filter.value"
                    :class="['px-3', 'py-1.5', 'text-xs', 'font-medium', 'rounded-full', 'transition-colors',
                        statusFilter === filter.value ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground hover:bg-accent']">
                    {{ filter.label }}
                </button>
            </div>

            <Transition mode="out-in" enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-150 ease-in" leave-from-class="opacity-100"
                leave-to-class="opacity-0">
                <div :key="activeTab + statusFilter">
                    <div v-if="filteredList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Card v-for="sol in filteredList" :key="sol.idsolicitud"
                            class="hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <CardHeader class="pb-2">
                                <div class="flex justify-between items-start gap-2">
                                    <CardTitle class="text-base">{{ sol.publicacion?.nombre || 'Material' }}</CardTitle>
                                    <span
                                        :class="['inline-flex', 'items-center', 'px-2.5', 'py-1', 'rounded-full', 'text-xs', 'font-semibold', 'border', getEstadoBadge(sol.estado)]">
                                        {{ sol.estado }}
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">{{ timeAgo(sol.created_at) }}</p>
                            </CardHeader>
                            <CardContent>
                                <div v-if="activeTab === 'recibidas'" class="mb-3">
                                    <p class="text-sm text-muted-foreground">
                                        <strong>Solicitante:</strong> {{ sol.empresa_origen?.nombreEmpresa || 'N/A' }}
                                    </p>
                                </div>
                                <div v-else class="mb-3">
                                    <p class="text-sm text-muted-foreground">
                                        <strong>Empresa:</strong> {{ sol.publicacion?.empresa?.nombreEmpresa || 'N/A' }}
                                    </p>
                                </div>
                                <p class="text-sm text-muted-foreground mb-3">
                                    <strong>Mensaje:</strong> {{ sol.mensaje }}
                                </p>
                                <p class="text-xs text-muted-foreground mb-2">
                                    Cantidad: <strong class="text-foreground">{{ sol.cantidad || 0 }} {{
                                        sol.publicacion?.unidadMedida || '' }}</strong>
                                </p>

                                <template v-if="activeTab === 'recibidas'">
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
                                    <div v-if="sol.estado === 'Aceptado'" class="mt-3">
                                        <Button size="sm" @click="abrirConfirmacion(sol.idsolicitud, 'Completado')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white w-full">
                                            <CheckCircle2 class="w-4 h-4 mr-1" />
                                            Marcar como Completado
                                        </Button>
                                    </div>
                                </template>

                                <div v-if="sol.estado === 'Aceptado' || sol.estado === 'Completado'"
                                    class="mt-3 border-t border-border pt-3">
                                    <Link :href="`/chat/${sol.idsolicitud}`">
                                        <Button size="sm" class="w-full"
                                            :class="activeTab === 'recibidas' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'border-primary text-primary hover:bg-accent'"
                                            :variant="activeTab === 'enviadas' ? 'outline' : 'default'">
                                            💬 {{ activeTab === 'recibidas' ? `Ir al chat con el solicitante` : `Ir al
                                            chat` }}
                                        </Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <Card v-else class="border-dashed">
                        <CardContent class="text-center text-muted-foreground py-12">
                            <component :is="activeTab === 'recibidas' ? Inbox : Send"
                                class="w-12 h-12 mx-auto mb-3 opacity-30" />
                            <p class="text-lg">No hay solicitudes {{ activeTab === 'recibidas' ? 'recibidas' :
                                'enviadas' }}</p>
                            <p v-if="statusFilter !== 'all'" class="text-sm mt-2">Prueba con otro filtro</p>
                            <p v-else class="text-sm mt-2">{{ activeTab === 'recibidas' ? `Cuando alguien te solicite un
                                material, aparecerá
                                aquí` : `Cuando solicites un material, aparecerá aquí` }}</p>
                        </CardContent>
                    </Card>
                </div>
            </Transition>
        </div>

        <AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
            <AlertDialogContent class="bg-card border-border text-foreground">
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {{ accionPendiente?.nuevoEstado === 'Aceptado' ? 'Aceptar solicitud'
                            : accionPendiente?.nuevoEstado === 'Completado' ? 'Completar intercambio'
                                : 'Rechazar solicitud' }}
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-muted-foreground">
                        ¿Estás seguro de que deseas
                        <strong>{{ accionPendiente?.nuevoEstado === 'Aceptado' ? `aceptar`
                            : accionPendiente?.nuevoEstado === 'Completado' ? `marcar como completado` : `rechazar`
                            }}</strong>
                        esta solicitud de intercambio?
                        {{ accionPendiente?.nuevoEstado === 'Completado' ? ` El material se registrará como
                        intercambiado en los
                        reportes.` : ` Esta acción actualizará el estado de forma inmediata.` }}
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        Cancelar</AlertDialogCancel>
                    <AlertDialogAction @click="confirmarCambioEstado" :class="accionPendiente?.nuevoEstado === 'Aceptado'
                        ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                        : accionPendiente?.nuevoEstado === 'Completado'
                            ? 'bg-blue-600 hover:bg-blue-700 text-white'
                            : 'bg-destructive hover:bg-destructive/90 text-destructive-foreground'">
                        Sí, {{ accionPendiente?.nuevoEstado === 'Aceptado' ? 'aceptar'
                            : accionPendiente?.nuevoEstado === 'Completado' ? 'completar' : 'rechazar' }}
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