<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Ban, Unlock, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const page = usePage();
const showNotificacion = ref(false);
const notificaciones = ref<any[]>([]);
const indexActual = ref(0);
const showDesbloqueo = ref(false);
const desbloqueoData = ref<any>(null);
let yaSuscrito = false;

function agregarNotificacion(data: any) {
    const yaExiste = notificaciones.value.some(n =>
        n.nombreBloqueadora === data.nombreBloqueadora
    );
    if (yaExiste) return;

    notificaciones.value.push({
        idnotificacion: 'temp-' + Date.now(),
        nombreBloqueadora: data.nombreBloqueadora,
        motivo: data.motivo,
        fecha: new Date().toLocaleString('es-ES'),
        idempresa_bloqueadora: data.idempresa_bloqueadora,
    });

    if (!showNotificacion.value) {
        indexActual.value = notificaciones.value.length - 1;
        showNotificacion.value = true;
    }
}

function mostrarDesbloqueo(data: any) {
    desbloqueoData.value = data;
    showDesbloqueo.value = true;
    setTimeout(() => {
        showDesbloqueo.value = false;
    }, 5000);
}

onMounted(() => {
    notificaciones.value = (page.props.notificacionesBloqueo as any[]) || [];

    const empresaId = (page.props.auth as any)?.user?.idempresa;

    if (notificaciones.value.length > 0) {
        showNotificacion.value = true;
    }

    if (typeof window !== 'undefined' && window.Echo && empresaId) {
        if ((window as any).__notifBloqueoCount && (window as any).__notifBloqueoCount > 0) {
            (window as any).__notifBloqueoCount++;
            return;
        }
        (window as any).__notifBloqueoCount = 1;
        yaSuscrito = true;

        window.Echo.private(`empresa.${empresaId}`)
            .listen('.empresa.bloqueada', (data: any) => {
                agregarNotificacion(data);
            })
            .listen('.empresa.desbloqueada', (data: any) => {
                notificaciones.value = notificaciones.value.filter(
                    n => n.nombreBloqueadora !== data.nombreBloqueadora
                );
                if (notificaciones.value.length === 0) {
                    showNotificacion.value = false;
                }
                mostrarDesbloqueo(data);
            });
    }
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        (window as any).__notifBloqueoCount = Math.max(0, ((window as any).__notifBloqueoCount || 0) - 1);

        if ((window as any).__notifBloqueoCount === 0 && yaSuscrito) {
            const empresaId = (page.props.auth as any)?.user?.idempresa;
            if (typeof window !== 'undefined' && window.Echo && empresaId) {
                window.Echo.leave(`empresa.${empresaId}`);
            }
            yaSuscrito = false;
        }
    }
});

function cerrarYMarcarLeida() {
    const notif = notificaciones.value[indexActual.value];

    if (!notif) {
        showNotificacion.value = false;
        notificaciones.value = [];
        return;
    }

    if (String(notif.idnotificacion).startsWith('temp-')) {
        if (notif.idempresa_bloqueadora) {
            router.post('/notificaciones/marcar-leida-empresa/' + notif.idempresa_bloqueadora, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
        notificaciones.value = notificaciones.value.filter(
            n => n.idnotificacion !== notif.idnotificacion
        );
        if (indexActual.value >= notificaciones.value.length) {
            indexActual.value = Math.max(0, notificaciones.value.length - 1);
        }
        if (notificaciones.value.length === 0) {
            showNotificacion.value = false;
        }
        return;
    }

    router.post('/notificaciones/marcar-leida/' + notif.idnotificacion, {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            notificaciones.value = notificaciones.value.filter(
                n => n.idnotificacion !== notif.idnotificacion
            );
            if (indexActual.value >= notificaciones.value.length) {
                indexActual.value = Math.max(0, notificaciones.value.length - 1);
            }
            if (notificaciones.value.length === 0) {
                showNotificacion.value = false;
            }
        }
    });
}

const notifActual = () => notificaciones.value[indexActual.value];
</script>

<template>
    <Transition enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-[-10px] scale-95" enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-300 ease-in" leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-[-10px] scale-95">
        <div v-if="showNotificacion && notifActual()"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-[99999] w-[460px] rounded-xl border border-red-300 dark:border-red-800 bg-card shadow-2xl overflow-hidden">
            <div class="bg-red-600 p-3 flex items-center gap-2">
                <Ban class="h-5 w-5 text-white" />
                <p class="font-semibold text-white">Has sido bloqueado</p>
                <button @click="cerrarYMarcarLeida" class="ml-auto text-white hover:bg-red-700 rounded p-1">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <div class="p-4 space-y-3">
                <p class="text-sm text-foreground">
                    La empresa <strong>{{ notifActual().nombreBloqueadora }}</strong>
                    te ha bloqueado. Ya no podrás enviar mensajes ni solicitudes a esta empresa.
                </p>

                <div v-if="notifActual().motivo" class="p-3 rounded-md bg-muted">
                    <p class="text-xs font-semibold text-muted-foreground mb-1">Motivo del bloqueo:</p>
                    <p class="text-sm text-foreground">{{ notifActual().motivo }}</p>
                </div>

                <p v-if="notificaciones.length > 1" class="text-xs text-muted-foreground text-center">
                    Notificación {{ indexActual + 1 }} de {{ notificaciones.length }}
                </p>

                <div class="flex justify-end gap-2">
                    <Button @click="cerrarYMarcarLeida" class="bg-red-600 hover:bg-red-700 text-white">
                        {{ indexActual < notificaciones.length - 1 ? 'Siguiente' : 'Entendido' }} </Button>
                </div>
            </div>
        </div>
    </Transition>

    <Transition enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-[-10px] scale-95" enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-300 ease-in" leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-[-10px] scale-95">
        <div v-if="showDesbloqueo && desbloqueoData"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-[99999] w-[460px] rounded-xl border border-green-300 dark:border-green-800 bg-card shadow-2xl overflow-hidden">
            <div class="bg-green-600 p-3 flex items-center gap-2">
                <Unlock class="h-5 w-5 text-white" />
                <p class="font-semibold text-white">Has sido desbloqueado</p>
                <button @click="showDesbloqueo = false" class="ml-auto text-white hover:bg-green-700 rounded p-1">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <div class="p-4 space-y-3">
                <p class="text-sm text-foreground">
                    La empresa <strong>{{ desbloqueoData.nombreBloqueadora }}</strong>
                    te ha desbloqueado. Ya puedes enviar mensajes y solicitudes nuevamente.
                </p>

                <div class="flex justify-end gap-2">
                    <Button @click="showDesbloqueo = false" class="bg-green-600 hover:bg-green-700 text-white">
                        Entendido
                    </Button>
                </div>
            </div>
        </div>
    </Transition>
</template>