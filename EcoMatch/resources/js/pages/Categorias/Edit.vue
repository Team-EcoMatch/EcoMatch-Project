<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, X, Save, AlertCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Categoria {
    idcategorias: number;
    nombre: string;
    descripcion: string | null;
}

const props = defineProps<{
    categoria: Categoria;
}>();

const form = useForm({
    nombre: props.categoria.nombre,
    descripcion: props.categoria.descripcion || '',
});

const showNotification = ref(false);
const notificationMessage = ref('');

function triggerError(msg: string) {
    notificationMessage.value = msg;
    showNotification.value = true;
    setTimeout(() => {
        showNotification.value = false;
    }, 3000);
}

function submit() {
    form.put('/categorias/' + props.categoria.idcategorias, {
        onSuccess: () => {},
        onError: (errors) => {
            if (errors.nombre) {
                triggerError(errors.nombre);
            }
        }
    });
}
</script>

<template>
    <div class="p-6 md:p-10 bg-background min-h-screen text-foreground">
        <Head title="Editar Categoría" />

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
                        <p class="font-semibold text-foreground">
                            Error al guardar
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
                    <div class="h-full bg-red-500 animate-[toast-progress_3s_linear_forwards]"></div>
                </div>
            </div>
        </Transition>

        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight mb-2">Editar Categoría</h1>
                    <p class="text-muted-foreground">Actualiza los detalles de la categoría seleccionada.</p>
                </div>
                <Link href="/categorias">
                    <Button variant="outline" class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        <ArrowLeft class="w-4 h-4 mr-2 text-foreground" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card class="bg-card border-border shadow-none overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border">
                        <CardTitle>Información de la Categoría</CardTitle>
                        <CardDescription>Modifica los campos y guarda los cambios cuando termines.</CardDescription>
                    </CardHeader>

                    <CardContent class="p-6 space-y-6">
                        <div>
                            <Label for="edit_nombre" class="text-muted-foreground mb-2 block">Nombre de la Categoría</Label>
                            <Input id="edit_nombre" v-model="form.nombre" 
                                class="bg-background border-border text-foreground focus-visible:ring-primary" />
                        </div>
                        <div>
                            <Label for="edit_description" class="text-muted-foreground mb-2 block">Descripción (Opcional)</Label>
                            <Input id="edit_descripcion" v-model="form.descripcion"
                                class="bg-background border-border text-foreground focus-visible:ring-primary" />
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-between border-t border-border bg-muted/20 p-6">
                        <Link href="/categorias">
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
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }
</style>