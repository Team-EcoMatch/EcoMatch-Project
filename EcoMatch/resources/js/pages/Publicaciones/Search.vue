<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { Head, router, Link, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import { Search, MapPin, Filter, ArrowLeft, ArrowLeftRight, CheckCircle2, X } from 'lucide-vue-next';

const props = defineProps<{
    publicaciones: any;
    filtros: any;
    advertencia?: string;
}>();

// 🔍 Filtros que van al backend (solo ubicación y radio)
const form = reactive({
    lat: props.filtros?.lat || '',
    lng: props.filtros?.lng || '',
    radio: props.filtros?.radio || 50,
});

// 🔎 Búsqueda LOCAL
const searchQuery = ref('');
const buscando = ref(false);

// 📩 Toast de notificación
const showNotification = ref(false);
const notificationMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

function triggerNotification(msg: string, tipo: 'success' | 'error' = 'success') {
    notificationMessage.value = msg;
    toastType.value = tipo;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 3000);
}

// 🎯 Filtrado en cliente
const filteredPublicaciones = computed(() => {
    if (!props.publicaciones?.data) return [];

    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.publicaciones.data;

    return props.publicaciones.data.filter((pub: any) => {
        const nombre = (pub.nombre || '').toLowerCase();
        const descripcion = (pub.descripcion || '').toLowerCase();
        const empresa = (pub.empresa?.nombreEmpresa || '').toLowerCase();
        const categoria = (pub.categoria?.nombre || '').toLowerCase();

        return (
            nombre.includes(query) ||
            descripcion.includes(query) ||
            empresa.includes(query) ||
            categoria.includes(query)
        );
    });
});

function obtenerUbicacion() {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            form.lat = pos.coords.latitude;
            form.lng = pos.coords.longitude;
            buscar();
        },
        (error) => {
            alert('Error al obtener ubicación: ' + error.message);
        }
    );
}

function buscar() {
    if (!form.lat || !form.lng) {
        alert('Por favor ingresa coordenadas o usa tu ubicación');
        return;
    }

    buscando.value = true;
    router.get('/buscar', {
        lat: form.lat,
        lng: form.lng,
        radio: form.radio,
    }, {
        preserveState: true,
        onFinish: () => { buscando.value = false; },
    });
}

function limpiarFiltros() {
    form.lat = '';
    form.lng = '';
    form.radio = 50;
    searchQuery.value = '';
    router.get('/buscar', {});
}

// ============================================================
// 📩 SOLICITUD DE INTERCAMBIO
// ============================================================
const solicitudForm = useForm({
    idpublicaciones: null as number | null,
    mensaje: '',
    cantidad: '',
});

const openSolicitudDialog = ref(false);

function abrirModalSolicitud(id: number, cantidadMax: number | string) {
    solicitudForm.idpublicaciones = id;
    solicitudForm.mensaje = '';
    solicitudForm.cantidad = '';
    solicitudForm.clearErrors();
    openSolicitudDialog.value = true;
}

function enviarSolicitud() {
    if (!solicitudForm.cantidad || parseFloat(solicitudForm.cantidad) <= 0) {
        triggerNotification('Debes especificar una cantidad válida.', 'error');
        return;
    }
    if (!solicitudForm.mensaje?.trim()) {
        triggerNotification('Escribe un mensaje.', 'error');
        return;
    }

    solicitudForm.post('/solicitudes', {
        preserveScroll: true,
        onSuccess: (page) => {
            openSolicitudDialog.value = false;
            solicitudForm.reset();
            const msg = (page.props.message as string) || 'Solicitud enviada correctamente.';
            triggerNotification(msg, 'success');
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors).flat().join('\n');
            triggerNotification('Error: ' + errorMsg, 'error');
        },
    });
}
</script>

<template>

    <Head title="Buscar Materiales" />

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-foreground flex items-center gap-2">
                    <Search class="w-6 h-6 text-primary" />
                    Buscar Materiales Cerca de Ti
                </h1>
                <p class="text-sm text-muted-foreground">
                    Encuentra materiales disponibles de otras empresas, ordenados por distancia.
                </p>
            </div>
            <Link href="/publicaciones">
                <Button variant="outline">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Volver
                </Button>
            </Link>
        </div>

        <!-- Advertencia (si la empresa no tiene coordenadas) -->
        <div v-if="advertencia"
            class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-lg">
            <p class="text-sm text-amber-800 dark:text-amber-300">
                ⚠️ {{ advertencia }}
            </p>
        </div>

        <!-- Filtros -->
        <Card class="mb-6">
            <CardContent class="p-4">
                <!-- Fila 1: Coordenadas y radio -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <Label class="text-xs">Latitud</Label>
                        <Input v-model="form.lat" type="number" step="any" placeholder="4.7110" />
                    </div>
                    <div>
                        <Label class="text-xs">Longitud</Label>
                        <Input v-model="form.lng" type="number" step="any" placeholder="-74.0721" />
                    </div>
                    <div>
                        <Label class="text-xs">Radio (km)</Label>
                        <Input v-model="form.radio" type="number" min="1" max="1000" />
                    </div>
                </div>
                <!-- barra de busqueda-->
                <div class="mt-4">
                    <Label class="text-xs">Buscar en los resultados</Label>
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="searchQuery"
                            placeholder="Filtrar por nombre, descripción, empresa o categoría..." class="pl-10" />
                    </div>
                </div>
                <!-- botones -->
                <div class="mt-4 flex flex-wrap gap-3">
                    <Button @click="obtenerUbicacion" type="button"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        📍 Usar mi ubicación
                    </Button>
                    <Button @click="buscar" :disabled="buscando" class="bg-primary text-primary-foreground">
                        <Filter class="w-4 h-4 mr-2" />
                        {{ buscando ? 'Buscando...' : 'Buscar' }}
                    </Button>
                    <Button variant="outline" @click="limpiarFiltros">Limpiar</Button>
                </div>
            </CardContent>
        </Card>

        <!-- resultados -->
        <div v-if="!publicaciones || filteredPublicaciones.length === 0"
            class="text-center py-12 text-muted-foreground border border-dashed rounded-lg">
            <MapPin class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p class="text-lg">
                {{ searchQuery ? 'No hay resultados que coincidan con tu búsqueda.' : 'No se encontraron materiales disponibles.' }}
            </p>
            <p class="text-sm mt-2">
                {{ searchQuery ? 'Prueba con otro término o limpia el filtro.' : 'Prueba ampliando el radio de busqueda.' }}
            </p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card v-for="pub in filteredPublicaciones" :key="pub.idpublicaciones"
                class="hover:shadow-md transition-shadow">
                <CardHeader class="pb-3">
                    <div class="flex justify-between items-start gap-2">
                        <CardTitle class="text-base">{{ pub.nombre }}</CardTitle>
                        <Badge variant="outline"
                            class="bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 shrink-0">
                            <MapPin class="w-3 h-3 mr-1" />
                            {{ pub.distancia_km }} km
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="aspect-video w-full overflow-hidden rounded-md bg-muted mb-4">
                        <img :src="pub.urlImagen" :alt="pub.nombre" class="w-full h-full object-cover"
                            @error="(e) => (e.target as HTMLImageElement).src = '/images/placeholder.png'" />
                    </div>

                    <p class="text-sm text-muted-foreground mb-3 line-clamp-2">{{ pub.descripcion }}</p>

                    <div class="text-xs text-muted-foreground space-y-0.5">
                        <div>Cantidad: <strong class="text-foreground">{{ pub.cantidad }} {{ pub.unidadMedida
                                }}</strong></div>
                        <div>Empresa: <strong class="text-foreground">{{ pub.empresa?.nombreEmpresa }}</strong></div>
                        <div>Categoría: <strong class="text-foreground">{{ pub.categoria?.nombre }}</strong></div>
                        <div>Frecuencia: <strong class="text-foreground">{{ pub.frecuencia }}</strong></div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-border mt-4 pt-4">
                        <Button size="sm" @click="abrirModalSolicitud(pub.idpublicaciones, pub.cantidad)"
                            class="bg-primary hover:bg-primary/90 text-primary-foreground">
                            <ArrowLeftRight class="mr-2 h-4 w-4" />
                            Solicitar Intercambio
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Paginación -->
        <div v-if="publicaciones && publicaciones.total > 0"
            class="mt-6 flex justify-between items-center text-sm text-muted-foreground">
            <div>Mostrando {{ publicaciones.from }} a {{ publicaciones.to }} de {{ publicaciones.total }}</div>
            <div class="flex gap-2">
                <button v-if="publicaciones.prev_page_url" @click="router.get(publicaciones.prev_page_url)"
                    class="px-3 py-1 border border-input rounded bg-card hover:bg-accent">
                    Anterior
                </button>
                <button v-if="publicaciones.next_page_url" @click="router.get(publicaciones.next_page_url)"
                    class="px-3 py-1 border border-input rounded bg-card hover:bg-accent">
                    Siguiente
                </button>
            </div>
        </div>
        <!-- Toast de notificación -->
        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showNotification"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                        :class="toastType === 'success' ? 'bg-green-500/10' : 'bg-red-500/10'">
                        <CheckCircle2 class="h-5 w-5"
                            :class="toastType === 'success' ? 'text-green-500' : 'text-red-500'" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-foreground">
                            {{ toastType === 'success' ? 'Acción completada' : 'Error' }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                    </div>
                    <button type="button" @click="showNotification = false"
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

        <!-- Modal de solicitud -->
        <Dialog :open="openSolicitudDialog" @update:open="openSolicitudDialog = $event">
            <DialogContent class="bg-card border-border text-foreground">
                <DialogHeader>
                    <DialogTitle>Solicitar Intercambio</DialogTitle>
                    <DialogDescription class="text-muted-foreground">
                        Escribe un mensaje y especifica la cantidad que deseas solicitar.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="mensaje" class="text-muted-foreground">Mensaje</Label>
                        <Textarea id="mensaje" v-model="solicitudForm.mensaje" rows="4"
                            placeholder="Hola, estamos interesados en tu material. ¿Lo intercambias por...?"
                            class="bg-background border-border text-foreground" />
                        <p v-if="solicitudForm.errors.mensaje" class="text-xs text-red-500">
                            {{ solicitudForm.errors.mensaje }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="cantidad" class="text-muted-foreground">Cantidad a solicitar</Label>
                        <Input id="cantidad" v-model="solicitudForm.cantidad" type="number" step="0.01" min="0.01"
                            placeholder="Ej: 50" class="bg-background border-border text-foreground" />
                        <p v-if="solicitudForm.errors.cantidad" class="text-xs text-red-500">
                            {{ solicitudForm.errors.cantidad }}
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="openSolicitudDialog = false"
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        Cancelar
                    </Button>
                    <Button @click="enviarSolicitud" :disabled="solicitudForm.processing"
                        class="bg-primary hover:bg-primary/90 text-primary-foreground">
                        <ArrowLeftRight class="w-4 h-4 mr-2" />
                        {{ solicitudForm.processing ? 'Enviando...' : 'Enviar Solicitud' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
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