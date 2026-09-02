<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, X, Save, AlertCircle, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

interface Categoria { idcategorias: number; nombre: string; }
interface Publicacion {
    idpublicaciones: number;
    idcategorias: number;
    nombre: string;
    descripcion: string;
    cantidad: string | number;
    unidadMedida: string;
    frecuencia: string;
    estado: string;
    urlImagen: string;
}

const props = defineProps<{ 
    publicacion: Publicacion;
    categorias: Categoria[];
}>();

const form = useForm({
    idcategorias: props.publicacion.idcategorias,
    nombre: props.publicacion.nombre,
    descripcion: props.publicacion.descripcion,
    cantidad: props.publicacion.cantidad,
    unidadMedida: props.publicacion.unidadMedida,
    frecuencia: props.publicacion.frecuencia,
    estado: props.publicacion.estado,
    urlImagen: null as File | null,
});

const urlImagenActual = props.publicacion.urlImagen;

const showNotification = ref(false);
const notificationMessage = ref('');

function triggerError(msg: string) {
    notificationMessage.value = msg;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 3000); 
}

function triggerFileInput() {
    document.getElementById('urlImagen')?.click();
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.urlImagen = target.files[0];
    }
}

function submit() {
    form.transform((data) => ({
        ...data,
        urlImagen_actual: urlImagenActual,
    })).put('/publicaciones/' + props.publicacion.idpublicaciones, {
        onSuccess: () => {},
        onError: (errors) => {
            const firstError = Object.values(errors)[0] as string;
            if (firstError) {
                triggerError(firstError);
            }
        }
    });
}
</script>

<template>
    <div class="p-6 md:p-10 bg-background min-h-screen text-foreground">
        <Head title="Editar Publicación" />

        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showNotification"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-500/10">
                        <AlertCircle class="h-5 w-5 text-red-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-foreground">Error al guardar</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                    </div>
                    <button type="button" @click="showNotification = false" class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
                <div class="h-1 bg-muted">
                    <div class="h-full bg-red-500 animate-[toast-progress_3s_linear_forwards]"></div>
                </div>
            </div>
        </Transition>

        <div class="max-w-3xl mx-auto relative">
            <Link href="/publicaciones" class="hidden md:block absolute top-0 right-0">
                <Button variant="outline" class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                    <ArrowLeft class="w-4 h-4 mr-2 text-foreground" />
                    Volver
                </Button>
            </Link>

            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight mb-2">Editar Publicación</h1>
                <p class="text-muted-foreground">Actualiza los datos del material.</p>
            </div>

            <Card class="bg-card border-border shadow-none overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border">
                        <CardTitle>Datos del Material</CardTitle>
                        <CardDescription>Modifica la información y guarda los cambios.</CardDescription>
                    </CardHeader>

                    <CardContent class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="nombre" class="text-muted-foreground mb-2 block">Nombre del Material</Label>
                                <Input id="nombre" v-model="form.nombre" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                            </div>
                            <div>
                                <Label for="idcategorias" class="text-muted-foreground mb-2 block">Categoría</Label>
                                <select id="idcategorias" v-model="form.idcategorias" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary">
                                    <option v-for="cat in props.categorias" :key="cat.idcategorias" :value="cat.idcategorias">
                                        {{ cat.nombre }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <Label for="descripcion" class="text-muted-foreground mb-2 block">Descripción</Label>
                            <textarea id="descripcion" v-model="form.descripcion" rows="3" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <Label for="cantidad" class="text-muted-foreground mb-2 block">Cantidad</Label>
                                <Input id="cantidad" type="number" v-model="form.cantidad" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                            </div>
                            <div>
                                <Label for="unidadMedida" class="text-muted-foreground mb-2 block">Unidad de Medida</Label>
                                <select id="unidadMedida" v-model="form.unidadMedida" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary">
                                    <option value="Kilogramos">Kilogramos</option>
                                    <option value="Toneladas">Toneladas</option>
                                    <option value="Litros">Litros</option>
                                    <option value="Metros">Metros</option>
                                    <option value="Unidades">Unidades</option>
                                    <option value="Galones">Galones</option>
                                    <option value="Gramos">Gramos</option>
                                </select>
                            </div>
                            <div>
                                <Label for="frecuencia" class="text-muted-foreground mb-2 block">Frecuencia</Label>
                                <select id="frecuencia" v-model="form.frecuencia" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary">
                                    <option value="Único">Único</option>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Anual">Anual</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <Label for="estado" class="text-muted-foreground mb-2 block">Estado</Label>
                            <select id="estado" v-model="form.estado" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground focus-visible:ring-primary">
                                <option value="Disponible">Disponible</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Agotado">Agotado</option>
                                <option value="Reservado">Reservado</option>
                                <option value="Intercambiado">Intercambiado</option>
                            </select>
                        </div>

                        <div>
                            <Label class="text-muted-foreground mb-2 block">Imagen Actual</Label>
                            <img :src="urlImagenActual" alt="Imagen actual" class="w-40 h-40 object-cover rounded-md border border-border mb-4">
                            
                            <Label for="urlImagen" class="text-muted-foreground mb-2 block">Cambiar Imagen (Opcional)</Label>
                            <div class="flex items-center gap-4">
                                <Button type="button" variant="outline" @click="triggerFileInput" class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                    <Upload class="w-4 h-4 mr-2" />
                                    Seleccionar Nueva Imagen
                                </Button>
                                <span v-if="form.urlImagen" class="text-sm text-muted-foreground truncate">{{ form.urlImagen.name }}</span>
                            </div>
                            <Input id="urlImagen" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-between border-t border-border bg-muted/20 p-6">
                        <Link href="/publicaciones">
                            <Button type="button" variant="outline" class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                <X class="w-4 h-4 mr-2 text-destructive" />
                                Cancelar
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="bg-primary hover:bg-primary/90 text-primary-foreground">
                            <Save class="w-4 h-4 mr-2" />
                            Guardar Cambios
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>
</template>

<style>
    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>