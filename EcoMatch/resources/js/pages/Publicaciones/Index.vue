<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader,
    AlertDialogTitle, AlertDialogTrigger
} from '@/components/ui/alert-dialog';
import {
    Pencil, Trash2, CheckCircle2, XCircle, X, Plus, Search,
    PackageCheck, Clock, Layers
} from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';

interface Publicacion {
    idpublicaciones: number;
    idempresa: number;
    nombre: string;
    descripcion: string;
    cantidad: string | number;
    unidadMedida: string;
    frecuencia: string;
    estado: string;
    urlImagen: string;
    created_at: string;
    empresa: { nombreEmpresa: string } | null;
    categoria: { idcategorias: number; nombre: string } | null;
}

interface Categoria {
    idcategorias: number;
    nombre: string;
}

const props = defineProps<{
    publicaciones: Publicacion[];
    categorias: Categoria[];
    message: string | null;
}>();

const page = usePage();
const userRol = page.props.auth?.user?.rol;

const showNotification = ref(false);
const notificationMessage = ref(props.message || '');
const searchQuery = ref('');
const selectedCategory = ref('all');

const stats = computed(() => ({
    activas: props.publicaciones.filter(p => p.estado === 'Disponible').length,
    pendientes: props.publicaciones.filter(p => p.estado === 'Pendiente').length,
    total: props.publicaciones.length,
}));

const uniqueCategories = computed(() => {
    let cats = props.publicaciones.map(p => p.categoria).filter((c): c is { idcategorias: number; nombre: string } => c !== null);
    if (props.categorias) cats = cats.concat(props.categorias);
    const unique = Array.from(new Map(cats.map(c => [c.idcategorias, c])).values());
    return unique.sort((a, b) => a.nombre.localeCompare(b.nombre));
});

const filteredPublicaciones = computed(() => {
    return props.publicaciones.filter(pub => {
        const matchCategory = selectedCategory.value === 'all' || pub.categoria?.nombre === selectedCategory.value;
        const matchSearch = pub.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            pub.descripcion.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchCategory && matchSearch;
    });
});

function getEstadoBadge(estado: string) {
    const map: Record<string, string> = {
        'Disponible': 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'Pendiente': 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'Agotado': 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
        'Inactivo': 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        'Reservado': 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        'Intercambiado': 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
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
    if (days > 0) return `Hace ${days} día${days !== 1 ? 's' : ''}`;
    if (hours > 0) return `Hace ${hours} hora${hours !== 1 ? 's' : ''}`;
    return 'Hace un momento';
}

onMounted(() => {
    if (props.message) triggerNotification(props.message);
});

function triggerNotification(msg: string) {
    notificationMessage.value = msg;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 3000);
}

function deletePublicacion(id: number) {
    useForm({}).delete('/publicaciones/' + id, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            const msg = (page.props.message as string) || 'Publicación eliminada correctamente.';
            triggerNotification(msg);
        }
    });
}

const showModeracionDialog = ref(false);
const moderacionPendiente = ref<{ id: number; accion: 'aprobar' | 'rechazar' } | null>(null);

function abrirConfirmacionModeracion(id: number, accion: 'aprobar' | 'rechazar') {
    moderacionPendiente.value = { id, accion };
    showModeracionDialog.value = true;
}

function confirmarModeracion() {
    if (!moderacionPendiente.value) return;
    const { id, accion } = moderacionPendiente.value;
    const endpoint = accion === 'aprobar' ? `/admin/publicaciones/${id}/approve` : `/admin/publicaciones/${id}/reject`;
    const mensajeDefault = accion === 'aprobar' ? 'Publicación aprobada.' : 'Publicación rechazada.';

    router.patch(endpoint, {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            const msg = (page.props.message as string) || mensajeDefault;
            triggerNotification(msg);
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors).flat().join('\n');
            triggerNotification('Error: ' + errorMsg);
        },
        onFinish: () => {
            showModeracionDialog.value = false;
            moderacionPendiente.value = null;
        }
    });
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Publicaciones" />

        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showNotification"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/10">
                        <CheckCircle2 class="h-5 w-5 text-green-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-foreground">Acción completada</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                    </div>
                    <button type="button" @click="showNotification = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold">Mis Publicaciones</h2>
                    <p class="text-sm text-muted-foreground mt-1">Gestiona los materiales de tu empresa</p>
                </div>
                <div class="flex gap-2">
                    <Button @click="router.visit('/buscar')" variant="outline"
                        class="border-primary text-primary hover:bg-accent">
                        <Search class="w-4 h-4 mr-2" />
                        Buscar materiales
                    </Button>
                    <Button @click="router.visit('/publicaciones/create')"
                        class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg">
                        <Plus class="w-4 h-4 mr-2" />
                        Crear
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-6">
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                            <PackageCheck class="h-5 w-5 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-foreground tabular-nums">{{ stats.activas }}</p>
                            <p class="text-xs text-muted-foreground">Activas</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-amber-500/20 bg-gradient-to-br from-amber-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/15 shrink-0">
                            <Clock class="h-5 w-5 text-amber-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-foreground tabular-nums">{{ stats.pendientes }}</p>
                            <p class="text-xs text-muted-foreground">Pendientes</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/15 shrink-0">
                            <Layers class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-foreground tabular-nums">{{ stats.total }}</p>
                            <p class="text-xs text-muted-foreground">Total</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                    <Input v-model="searchQuery" placeholder="Buscar material..."
                        class="pl-10 bg-background border-border text-foreground focus-visible:ring-primary" />
                </div>
                <select v-model="selectedCategory"
                    class="h-10 w-full md:w-[200px] rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary">
                    <option value="all">Todas las categorías</option>
                    <option v-for="cat in uniqueCategories" :key="cat.idcategorias" :value="cat.nombre">
                        {{ cat.nombre }}
                    </option>
                </select>
            </div>

            <div v-if="filteredPublicaciones.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="pub in filteredPublicaciones" :key="pub.idpublicaciones"
                    class="overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="relative">
                        <img :src="pub.urlImagen" alt="Imagen material" class="w-full h-40 object-cover"
                            @error="(e) => (e.target as HTMLImageElement).src = '/images/placeholder.png'" />
                        <div class="absolute top-2 right-2">
                            <span
                                :class="['inline-flex', 'items-center', 'px-2.5', 'py-1', 'rounded-full', 'text-xs', 'font-semibold', 'border', 'backdrop-blur-md', getEstadoBadge(pub.estado)]">
                                {{ pub.estado }}
                            </span>
                        </div>
                        <div class="absolute bottom-2 left-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-black/60 text-white backdrop-blur-md">
                                {{ timeAgo(pub.created_at) }}
                            </span>
                        </div>
                    </div>

                    <CardHeader class="pb-2">
                        <CardTitle class="text-lg">{{ pub.nombre }}</CardTitle>
                    </CardHeader>

                    <CardContent class="flex-grow flex flex-col justify-between">
                        <div class="mb-4">
                            <p class="text-sm text-muted-foreground mb-3 line-clamp-2">{{ pub.descripcion }}</p>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-muted-foreground">Cantidad:</span>
                                <span class="text-sm font-bold text-foreground">{{ pub.cantidad }} {{ pub.unidadMedida
                                    }}</span>
                            </div>
                            <div class="text-xs text-muted-foreground space-y-0.5">
                                <div>Categoría: <strong class="text-foreground">{{ pub.categoria?.nombre || 'N/A'
                                        }}</strong></div>
                                <div>Frecuencia: <strong class="text-foreground">{{ pub.frecuencia }}</strong></div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-border pt-4">
                            <template v-if="pub.estado === 'Pendiente' && userRol === 'Jefe'">
                                <Button size="sm" @click="abrirConfirmacionModeracion(pub.idpublicaciones, 'aprobar')"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white">
                                    <CheckCircle2 class="w-4 h-4 mr-1" />
                                    Aprobar
                                </Button>
                                <Button size="sm" @click="abrirConfirmacionModeracion(pub.idpublicaciones, 'rechazar')"
                                    class="bg-amber-500 hover:bg-amber-600 text-white">
                                    <XCircle class="w-4 h-4 mr-1" />
                                    Rechazar
                                </Button>
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="destructive">
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Eliminar
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent class="bg-card border-border text-foreground">
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                            <AlertDialogDescription class="text-muted-foreground">
                                                Se eliminará permanentemente la publicación "{{ pub.nombre }}".
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel
                                                class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                                Cancelar</AlertDialogCancel>
                                            <AlertDialogAction @click="deletePublicacion(pub.idpublicaciones)"
                                                class="bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                                                Sí, eliminar</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </template>

                            <template v-else-if="pub.estado === 'Pendiente' && userRol === 'Empresa'">
                                <span class="text-xs text-muted-foreground italic self-center">Pendiente de
                                    aprobación</span>
                            </template>

                            <template v-else>
                                <Link :href="`/publicaciones/${pub.idpublicaciones}/edit`">
                                    <Button size="sm" variant="outline"
                                        class="border-primary text-primary hover:bg-accent hover:text-primary">
                                        <Pencil class="mr-2 h-4 w-4" />
                                        Editar
                                    </Button>
                                </Link>
                                <AlertDialog>
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" variant="destructive">
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Eliminar
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent class="bg-card border-border text-foreground">
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                            <AlertDialogDescription class="text-muted-foreground">
                                                Se eliminará permanentemente la publicación "{{ pub.nombre }}".
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel
                                                class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                                Cancelar</AlertDialogCancel>
                                            <AlertDialogAction @click="deletePublicacion(pub.idpublicaciones)"
                                                class="bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                                                Sí, eliminar</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <PackageCheck class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p class="text-lg">No se encontraron publicaciones</p>
                    <p class="text-sm mt-2">Prueba con otra búsqueda o crea una nueva publicación</p>
                </CardContent>
            </Card>
        </div>

        <AlertDialog :open="showModeracionDialog" @update:open="showModeracionDialog = $event">
            <AlertDialogContent class="bg-card border-border text-foreground">
                <AlertDialogHeader>
                    <AlertDialogTitle>
                        {{ moderacionPendiente?.accion === 'aprobar' ? 'Aprobar publicación' : 'Rechazar publicación' }}
                    </AlertDialogTitle>
                    <AlertDialogDescription class="text-muted-foreground">
                        <template v-if="moderacionPendiente?.accion === 'aprobar'">
                            ¿Estás seguro de que deseas <strong>aprobar</strong> esta publicación? Pasará a estar
                            disponible para todas las empresas.
                        </template>
                        <template v-else>
                            ¿Estás seguro de que deseas <strong>rechazar</strong> esta publicación? No será visible para
                            otras empresas.
                        </template>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        Cancelar</AlertDialogCancel>
                    <AlertDialogAction @click="confirmarModeracion" :class="moderacionPendiente?.accion === 'aprobar'
                        ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                        : 'bg-amber-500 hover:bg-amber-600 text-white'">
                        Sí, {{ moderacionPendiente?.accion === 'aprobar' ? 'aprobar' : 'rechazar' }}
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