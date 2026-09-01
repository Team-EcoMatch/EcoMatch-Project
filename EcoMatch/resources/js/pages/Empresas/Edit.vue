<script setup lang="ts">
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, X, CheckCircle2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { dashboard } from '@/routes';

interface Empresa {
    idempresa: number;
    nombreEmpresa: string;
    direccion: string;
    email: string;
    telefono: string;
    tipoEmpresa: string;
    latitud: number | null;
    longitud: number | null;
    radioOperacion: number;
    estado: boolean | number;
}

const props = defineProps<{
    empresa: Empresa;
}>();

const page = usePage();

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/dashboard'
);

const form = useForm({
    nombreEmpresa: props.empresa.nombreEmpresa,
    direccion: props.empresa.direccion,
    email: props.empresa.email,
    telefono: props.empresa.telefono,
    tipoEmpresa: props.empresa.tipoEmpresa,
    
    latitud: props.empresa.latitud ?? '',
    longitud: props.empresa.longitud ?? '',
    
    radioOperacion: props.empresa.radioOperacion,
    estado: Boolean(props.empresa.estado),
});

const showNotification = ref(false);

function submit() {
    form.put(`/empresas/${props.empresa.idempresa}`, {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true;
            setTimeout(() => {
                showNotification.value = false;
                router.visit(dashboardUrl.value);
            }, 2500);
        },
    });
}
</script>

<template>
    <div class="p-6 md:p-10 bg-background min-h-screen text-foreground">
        <Head title="Editar Empresa" />

        <Transition 
            enter-active-class="transition-all duration-300 ease-out"
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
                        <p class="font-semibold text-foreground">Perfil Actualizado</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Los datos de tu empresa se guardaron correctamente.
                        </p>
                    </div>
                    <button type="button" @click="showNotification = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
                <div class="h-1 bg-muted">
                    <div class="h-full bg-green-500 animate-[toast-progress_2.5s_linear_forwards]"></div>
                </div>
            </div>
        </Transition>

        <div class="max-w-3xl mx-auto relative">
            <Link :href="dashboardUrl" class="hidden md:block absolute top-0 right-0">
                <Button variant="outline" class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                    <ArrowLeft class="w-4 h-4 mr-2 text-foreground" />
                    Volver al Dashboard
                </Button>
            </Link>

            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight mb-2">Perfil de la Empresa</h1>
                <p class="text-muted-foreground">
                    Actualiza la información de contacto y operación de tu empresa.
                </p>
            </div>

            <Card class="bg-card border-border shadow-none overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border">
                        <CardTitle>Información de la Empresa</CardTitle>
                        <CardDescription>Estos datos aparecerán en tus publicaciones y en el mapa.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="nombreEmpresa" class="text-muted-foreground mb-2 block">Nombre de la Empresa</Label>
                                <Input id="nombreEmpresa" v-model="form.nombreEmpresa" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.nombreEmpresa" class="text-destructive text-sm mt-2">{{ form.errors.nombreEmpresa }}</p>
                            </div>
                            <div>
                                <Label for="tipoEmpresa" class="text-muted-foreground mb-2 block">Tipo de Empresa</Label>
                                <Input id="tipoEmpresa" v-model="form.tipoEmpresa" placeholder="Ej: Recicladora" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.tipoEmpresa" class="text-destructive text-sm mt-2">{{ form.errors.tipoEmpresa }}</p>
                            </div>
                        </div>

                        <div>
                            <Label for="direccion" class="text-muted-foreground mb-2 block">Dirección</Label>
                            <Input id="direccion" v-model="form.direccion" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                            <p v-if="form.errors.direccion" class="text-destructive text-sm mt-2">{{ form.errors.direccion }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="email" class="text-muted-foreground mb-2 block">Email Corporativo</Label>
                                <Input id="email" type="email" v-model="form.email" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.email" class="text-destructive text-sm mt-2">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <Label for="telefono" class="text-muted-foreground mb-2 block">Teléfono</Label>
                                <Input id="telefono" v-model="form.telefono" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.telefono" class="text-destructive text-sm mt-2">{{ form.errors.telefono }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <Label for="latitud" class="text-muted-foreground mb-2 block">Latitud (Mapa)</Label>
                                <Input id="latitud" type="number" step="any" v-model.number="form.latitud" placeholder="Ej: 4.7110" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.latitud" class="text-destructive text-sm mt-2">{{ form.errors.latitud }}</p>
                            </div>
                            <div>
                                <Label for="longitud" class="text-muted-foreground mb-2 block">Longitud (Mapa)</Label>
                                <Input id="longitud" type="number" step="any" v-model.number="form.longitud" placeholder="Ej: -74.0721" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.longitud" class="text-destructive text-sm mt-2">{{ form.errors.longitud }}</p>
                            </div>
                            <div>
                                <Label for="radioOperacion" class="text-muted-foreground mb-2 block">Radio (Km)</Label>
                                <Input id="radioOperacion" type="number" v-model.number="form.radioOperacion" class="bg-background border-border text-foreground focus-visible:ring-primary" />
                                <p v-if="form.errors.radioOperacion" class="text-destructive text-sm mt-2">{{ form.errors.radioOperacion }}</p>
                            </div>
                        </div>
                    </CardContent>
                    
                    <CardFooter class="flex justify-between border-t border-border bg-muted/20 p-6">
                        <Link :href="dashboardUrl">
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