<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
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
import { Pencil, Trash2, CheckCircle2, X, Plus } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

interface Publicacion {
    idpublicaciones: number;
    nombre: string;
    descripcion: string;
    cantidad: string | number;
    unidadMedida: string;
    frecuencia: string;
    estado: string;
    urlImagen: string;
    empresa: { nombreEmpresa: string } | null;
    categoria: { nombre: string } | null;
}

const props = defineProps<{
    publicaciones: Publicacion[];
    message: string | null;
}>();

const showNotification = ref(false);
const notificationMessage = ref(props.message || '');

onMounted(() => {
    if (props.message) {
        triggerNotification(props.message);
    }
});

function triggerNotification(msg: string) {
    notificationMessage.value = msg;
    showNotification.value = true;
    setTimeout(() => {
        showNotification.value = false;
    }, 3000); 
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
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">
        <Head title="Publicaciones"/>

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
                        <p class="font-semibold text-foreground">
                            Acción completada
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ notificationMessage }}
                        </p>
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
            
            <div v-if="props.publicaciones.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="pub in props.publicaciones" :key="pub.idpublicaciones" class="bg-card border-border shadow-none flex flex-col justify-between overflow-hidden">
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
                                <div>Frecuencia: {{ pub.frecuencia }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-border pt-4">
                            <Link :href="`/publicaciones/${pub.idpublicaciones}/edit`">
                                <Button size="sm" variant="outline" class="border-primary text-primary hover:bg-accent hover:text-primary">
                                    <Pencil class="mr-2 h-4 w-4"/>
                                    <span>Editar</span>
                                </Button>
                            </Link>
                            
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button size="sm" variant="destructive" class="bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                                        <Trash2 class="mr-2 h-4 w-4"/>
                                        <span>Eliminar</span>
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent class="bg-card border-border text-foreground">
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                        <AlertDialogDescription class="text-muted-foreground">
                                            Esta acción no se puede deshacer. Se eliminará permanentemente la publicación "{{ pub.nombre }}".
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                            Cancelar
                                        </AlertDialogCancel>
                                        <AlertDialogAction @click="deletePublicacion(pub.idpublicaciones)" class="bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                                            Sí, eliminar
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else class="bg-card border-border shadow-none">
                <CardContent class="text-center text-muted-foreground py-12">
                    <p class="text-lg">No hay publicaciones registradas todavía.</p>
                    <p class="text-sm mt-2">¡Crea la primera publicación!</p>
                </CardContent>
            </Card>
        </div>
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