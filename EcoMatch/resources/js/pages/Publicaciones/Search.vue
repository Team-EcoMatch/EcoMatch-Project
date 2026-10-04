<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, router, Link, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import {
    Dialog, DialogContent, DialogDescription, DialogFooter,
    DialogHeader, DialogTitle
} from '@/components/ui/dialog';
import { Search, MapPin, Filter, ArrowLeft, ArrowLeftRight, CheckCircle2, X, PackageSearch, Crosshair } from 'lucide-vue-next';

const props = defineProps<{
    publicaciones: any;
    filtros: any;
    advertencia?: string;
}>();

const form = reactive({
    lat: props.filtros?.lat || '',
    lng: props.filtros?.lng || '',
    radio: props.filtros?.radio || 50,
});

const searchQuery = ref('');
const buscando = ref(false);
const showNotification = ref(false);
const notificationMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
const tieneCoordenadas = computed(() => form.lat && form.lng);
const hasSearched = ref(!!props.filtros?.lat && !!props.filtros?.lng);

function triggerNotification(msg: string, tipo: 'success' | 'error' = 'success') {
    notificationMessage.value = msg;
    toastType.value = tipo;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 3000);
}

const filteredPublicaciones = computed(() => {
    if (!props.publicaciones?.data) return [];
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.publicaciones.data;
    return props.publicaciones.data.filter((pub: any) => {
        const nombre = (pub.nombre || '').toLowerCase();
        const descripcion = (pub.descripcion || '').toLowerCase();
        const empresa = (pub.empresa?.nombreEmpresa || '').toLowerCase();
        const categoria = (pub.categoria?.nombre || '').toLowerCase();
        return nombre.includes(query) || descripcion.includes(query) || empresa.includes(query) || categoria.includes(query);
    });
});

onMounted(() => {
    if (tieneCoordenadas.value) {
        buscar();
    }
});

function obtenerUbicacion() {
    if (!navigator.geolocation) {
        triggerNotification('Tu navegador no soporta geolocalización', 'error');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            form.lat = pos.coords.latitude;
            form.lng = pos.coords.longitude;
            buscar();
        },
        () => {
            triggerNotification('No se pudo obtener tu ubicación. Ingresa las coordenadas manualmente.', 'error');
        }
    );
}

function buscar() {
    hasSearched.value = true;
    if (tieneCoordenadas.value) {
        buscando.value = true;
        router.get('/buscar', {
            lat: form.lat,
            lng: form.lng,
            radio: form.radio,
        }, {
            preserveState: true,
            onFinish: () => { buscando.value = false; },
        });
    } else {
        buscarTodos();
    }
}

function buscarTodos() {
    hasSearched.value = true;
    buscando.value = true;
    router.get('/buscar', {}, {
        preserveState: true,
        onFinish: () => { buscando.value = false; },
    });
}

function limpiarFiltros() {
    form.lat = '';
    form.lng = '';
    form.radio = props.filtros?.radio || 50;
    searchQuery.value = '';
    hasSearched.value = false;
}

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
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-foreground flex items-center gap-2">
                    <Search class="w-5 h-5 sm:w-6 sm:h-6 text-primary" />
                    Buscar Materiales
                </h1>
                <p class="text-sm text-muted-foreground">Encuentra materiales disponibles de otras empresas.</p>
            </div>
            <Link href="/publicaciones">
                <Button variant="outline" class="h-10">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Mis Publicaciones
                </Button>
            </Link>
        </div>

        <div v-if="advertencia"
            class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-lg">
            <p class="text-sm text-amber-800 dark:text-amber-300">⚠️ {{ advertencia }}</p>
        </div>

        <Card class="mb-6">
            <CardContent class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <Label class="text-xs text-muted-foreground">Latitud</Label>
                        <Input v-model="form.lat" type="number" step="any" placeholder="4.7110" class="mt-1" />
                    </div>
                    <div>
                        <Label class="text-xs text-muted-foreground">Longitud</Label>
                        <Input v-model="form.lng" type="number" step="any" placeholder="-74.0721" class="mt-1" />
                    </div>
                    <div>
                        <Label class="text-xs text-muted-foreground">Radio (km)</Label>
                        <Input v-model="form.radio" type="number" min="1" max="1000" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4">
                    <Label class="text-xs text-muted-foreground">Filtrar en los resultados</Label>
                    <div class="relative mt-1">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Nombre, descripción, empresa o categoría..."
                            class="pl-10" />
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <Button @click="obtenerUbicacion" type="button"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white">
                        <Crosshair class="w-4 h-4 mr-2" />
                        Mi ubicación
                    </Button>
                    <Button @click="buscar" :disabled="buscando" class="bg-primary text-primary-foreground">
                        <Filter class="w-4 h-4 mr-2" />
                        {{ buscando ? 'Buscando...' : (tieneCoordenadas ? 'Buscar cerca' : 'Ver todos') }}
                    </Button>
                    <Button variant="outline" @click="limpiarFiltros">Limpiar</Button>
                </div>
            </CardContent>
        </Card>

        <div v-if="tieneCoordenadas" class="mb-4 flex items-center gap-2 text-sm text-muted-foreground">
            <MapPin class="w-4 h-4 text-emerald-500" />
            Mostrando materiales a <strong class="text-foreground mx-1">{{ form.radio }} km</strong> de tu ubicación
        </div>

        <div v-if="!hasSearched" class="text-center py-12 text-muted-foreground border border-dashed rounded-lg">
            <PackageSearch class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p class="text-lg">Busca materiales disponibles</p>
            <p class="text-sm mt-2">Usa "Mi ubicación" para buscar cerca o "Ver todos" para ver todos los materiales</p>
        </div>

        <div v-else-if="!publicaciones || filteredPublicaciones.length === 0"
            class="text-center py-12 text-muted-foreground border border-dashed rounded-lg">
            <PackageSearch class="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p class="text-lg">{{ searchQuery ? `No hay resultados que coincidan con tu búsqueda.` : `No se encontraron
                materiales disponibles.` }}</p>
            <p class="text-sm mt-2">{{ searchQuery ? `Prueba con otro término o limpia el filtro.` : `Intenta ampliar el
                radio o ver todos los materiales.` }}</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <Card v-for="pub in filteredPublicaciones" :key="pub.idpublicaciones"
                class="hover:shadow-md transition-shadow">
                <CardHeader class="pb-3">
                    <div class="flex justify-between items-start gap-2">
                        <CardTitle class="text-base">{{ pub.nombre }}</CardTitle>
                        <Badge v-if="pub.distancia_km" variant="outline"
                            class="bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 shrink-0">
                            <MapPin class="w-3 h-3 mr-1" />
                            {{ pub.distancia_km }} km
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="aspect-video w-full overflow-hidden rounded-md bg-muted mb-4">
                        <img :src="pub.urlImagen || '/images/placeholder.png'" :alt="pub.nombre"
                            class="w-full h-full object-cover"
                            @error="(e) => (e.target as HTMLImageElement).src = '/images/placeholder.png'" />
                    </div>
                    <p class="text-sm text-muted-foreground mb-3 line-clamp-2">{{ pub.descripcion }}</p>
                    <div class="text-xs text-muted-foreground space-y-0.5">
                        <div>Cantidad: <strong class="text-foreground">{{ pub.cantidad }} {{ pub.unidadMedida
                                }}</strong></div>
                        <div>Empresa: <strong class="text-foreground">{{ pub.empresa?.nombreEmpresa }}</strong></div>
                        <div>Categoría: <strong class="text-foreground">{{ pub.categoria?.nombre }}</strong></div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-border mt-4 pt-4">
                        <Button size="sm" @click="abrirModalSolicitud(pub.idpublicaciones, pub.cantidad)"
                            class="bg-primary hover:bg-primary/90 text-primary-foreground">
                            <ArrowLeftRight class="mr-2 h-4 w-4" />
                            Solicitar
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="hasSearched && publicaciones && publicaciones.total > 0"
            class="mt-6 flex flex-col sm:flex-row sm:justify-between items-center text-sm text-muted-foreground gap-4">
            <div>Mostrando {{ publicaciones.from }} a {{ publicaciones.to }} de {{ publicaciones.total }}</div>
            <div class="flex gap-2">
                <button v-if="publicaciones.prev_page_url"
                    @click="router.get(publicaciones.prev_page_url, {}, { preserveState: true })"
                    class="px-3 py-1 border border-input rounded bg-card hover:bg-accent">Anterior</button>
                <button v-if="publicaciones.next_page_url"
                    @click="router.get(publicaciones.next_page_url, {}, { preserveState: true })"
                    class="px-3 py-1 border border-input rounded bg-card hover:bg-accent">Siguiente</button>
            </div>
        </div>

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
                        <p class="font-semibold text-foreground">{{ toastType === 'success' ? 'Acción completada' :
                            'Error' }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                    </div>
                    <button type="button" @click="showNotification = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>

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
                        <p v-if="solicitudForm.errors.mensaje" class="text-xs text-red-500">{{
                            solicitudForm.errors.mensaje }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="cantidad" class="text-muted-foreground">Cantidad a solicitar</Label>
                        <Input id="cantidad" v-model="solicitudForm.cantidad" type="number" step="0.01" min="0.01"
                            placeholder="Ej: 50" class="bg-background border-border text-foreground" />
                        <p v-if="solicitudForm.errors.cantidad" class="text-xs text-red-500">{{
                            solicitudForm.errors.cantidad }}</p>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="openSolicitudDialog = false"
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">Cancelar</Button>
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