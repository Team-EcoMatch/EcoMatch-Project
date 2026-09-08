<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger
} from '@/components/ui/alert-dialog';

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';

import { Pencil, Trash2, CheckCircle2, X, Plus, ArrowLeftRight, Search } from 'lucide-vue-next';
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
    idEmpresa: number;
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
const currentEmpresaId = page.props.auth.user.idempresa;
const userRol = page.props.auth.user.rol;

const showNotification = ref(false);
const notificationMessage = ref(props.message || '');

const showConfirmDialog = ref(false);
const solicitudPendiente = ref<{ id: number; mensaje: string } | null>(null);

const searchQuery = ref('');
const selectedCategory = ref('all');

const uniqueCategories = computed(() => {
    let cats = props.publicaciones.map(p => p.categoria).filter((c): c is { idcategorias: number; nombre: string } => c !== null);

    if (props.categorias) {
        cats = cats.concat(props.categorias);
    }

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

onMounted(() => {
    if (props.message) {
        triggerNotification(props.message);
    }
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

const solicitudForm = useForm({
    idpublicaciones: null as number | null,
    mensaje: ''
});

const openSolicitudDialog = ref(false);

function abrirModalSolicitud(id: number) {
    solicitudForm.idpublicaciones = id;
    solicitudForm.mensaje = '';
    openSolicitudDialog.value = true;
}

//modificado para enviar solicitud directamente
function enviarSolicitud() {
    if (!solicitudForm.mensaje?.trim()) {
        triggerNotification('Escriba un mensaje');
        return;
    }
    solicitudPendiente.value = {
        id: solicitudForm.idpublicaciones!,
        mensaje: solicitudForm.mensaje
    };

    openSolicitudDialog.value = false;
    showConfirmDialog.value = true;
}

function confirmarEnvio() {
    if (!solicitudPendiente.value) return;

    solicitudForm.post('/solicitudes', {
        preserveScroll: true,
        onSuccess: (page) => {
            showConfirmDialog.value = false;
            solicitudPendiente.value = null;
            const msg = (page.props.message as string) || 'Solicitud enviada correctamente.';
            triggerNotification(msg);
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors).flat().join('\n');
            triggerNotification('Error: ' + errorMsg);
        }
    });
}
function esDueno(pub: Publicacion): boolean {
    return pub.idempresa === currentEmpresaId;
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
                <div class="h-1 bg-muted">
                    <div class="h-full bg-green-500 animate-[toast-progress_3s_linear_forwards]"></div>
                </div>
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Materiales Publicados</h2>
                <Link href="/publicaciones/create">
                    <Button class="bg-primary hover:bg-primary/90 text-primary-foreground">
                        <Plus class="w-4 h-4 mr-2" />
                        Crear Publicación
                    </Button>
                </Link>
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

            <div v-if="filteredPublicaciones.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="pub in filteredPublicaciones" :key="pub.idpublicaciones"
                    class="bg-card border-border shadow-none flex flex-col justify-between overflow-hidden">
                    <img :src="pub.urlImagen" alt="Imagen material" class="w-full h-40 object-cover">

                    <CardHeader>
                        <div class="flex justify-between items-center">
                            <CardTitle class="text-xl">{{ pub.nombre }}</CardTitle>
                            <Badge variant="outline" class="border-primary text-primary">{{ pub.estado }}</Badge>
                        </div>
                    </CardHeader>

                    <CardContent class="flex-grow flex flex-col justify-between">
                        <div class="mb-4">
                            <p class="text-sm text-muted-foreground mb-2">{{ pub.descripcion }}</p>
                            <div class="flex justify-between text-sm font-semibold mb-2">
                                <span class="text-muted-foreground">Cantidad:</span>
                                <span>{{ pub.cantidad }} {{ pub.unidadMedida }}</span>
                            </div>
                            <div class="text-xs text-muted-foreground mt-2">
                                <div>Categoría: <strong>{{ pub.categoria?.nombre || 'N/A' }}</strong></div>
                                <div>Empresa: <strong>{{ pub.empresa?.nombreEmpresa || 'N/A' }}</strong></div>
                                <div>Frecuencia: {{ pub.frecuencia }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-border pt-4">
                            <template v-if="esDueno(pub) && userRol === 'Jefe'">
                                <Link :href="`/publicaciones/${pub.idpublicaciones}/edit`">
                                    <Button size="sm" variant="outline"
                                        class="border-primary text-primary hover:bg-accent hover:text-primary">
                                        <Pencil class="mr-2 h-4 w-4" />
                                        Editar
                                    </Button>
                                </Link>

                                <AlertDialog>
                                </AlertDialog>
                                <!-- AlertDialog de confirmación -->
                                <AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = $event">
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Confirmar solicitud</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                ¿Estás seguro de enviar esta solicitud de intercambio?
                                                <br><br>
                                                <strong>Mensaje:</strong> {{ solicitudPendiente?.mensaje }}
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel @click="showConfirmDialog = false">Cancelar
                                            </AlertDialogCancel>
                                            <AlertDialogAction @click="confirmarEnvio"
                                                class="bg-primary hover:bg-primary/90">
                                                Sí, enviar solicitud
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </template>

                            <template v-else-if="esDueno(pub) && userRol === 'Empresa'">
                                <span class="text-xs text-muted-foreground italic self-center">Pendiente de
                                    aprobación</span>
                            </template>

                            <template v-else>
                                <Button size="sm" @click="abrirModalSolicitud(pub.idpublicaciones)"
                                    class="bg-primary hover:bg-primary/90 text-primary-foreground">
                                    <ArrowLeftRight class="mr-2 h-4 w-4" />
                                    Solicitar Intercambio
                                </Button>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else class="bg-card border-border shadow-none">
                <CardContent class="text-center text-muted-foreground py-12">
                    <p class="text-lg">No se encontraron publicaciones.</p>
                    <p class="text-sm mt-2">Prueba con otra búsqueda.</p>
                </CardContent>
            </Card>
        </div>

        <Dialog :open="openSolicitudDialog" @update:open="openSolicitudDialog = $event">
            <DialogContent class="bg-card border-border text-foreground">
                <DialogHeader>
                    <DialogTitle>Solicitar Intercambio</DialogTitle>
                    <DialogDescription class="text-muted-foreground">
                        Escribe un mensaje para la empresa dueña del material.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="mensaje" class="text-muted-foreground">Mensaje</Label>
                        <Textarea id="mensaje" v-model="solicitudForm.mensaje" rows="4"
                            placeholder="Hola, estamos interesados en tu material. ¿Lo intercambias por...?"
                            class="bg-background border-border text-foreground" />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="openSolicitudDialog = false"
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        Cancelar
                    </Button>
                    <Button @click="enviarSolicitud" class="bg-primary hover:bg-primary/90 text-primary-foreground">
                        Enviar Solicitud
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