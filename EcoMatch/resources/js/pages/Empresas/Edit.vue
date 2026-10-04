<script setup lang="ts">
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, X, CheckCircle2, Building2, MapPin, Crosshair, Radio, Mail, Phone, Briefcase } from 'lucide-vue-next';
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
const ubicacionLoading = ref(false);

function getAvatarColor(nombre: string): { gradient: string; text: string } {
    const colors = [
        { gradient: 'from-blue-500/20 to-blue-500/5 border-blue-500/20', text: 'text-blue-500' },
        { gradient: 'from-emerald-500/20 to-emerald-500/5 border-emerald-500/20', text: 'text-emerald-500' },
        { gradient: 'from-amber-500/20 to-amber-500/5 border-amber-500/20', text: 'text-amber-500' },
        { gradient: 'from-purple-500/20 to-purple-500/5 border-purple-500/20', text: 'text-purple-500' },
        { gradient: 'from-pink-500/20 to-pink-500/5 border-pink-500/20', text: 'text-pink-500' },
    ];
    const hash = nombre.charCodeAt(0) % colors.length;
    return colors[hash];
}

const avatarConfig = computed(() => getAvatarColor(props.empresa.nombreEmpresa));
const tieneUbicacion = computed(() => form.latitud && form.longitud);

function obtenerUbicacion() {
    if (!navigator.geolocation) return;
    ubicacionLoading.value = true;
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            form.latitud = pos.coords.latitude;
            form.longitud = pos.coords.longitude;
            ubicacionLoading.value = false;
        },
        () => { ubicacionLoading.value = false; },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

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
    <div class="p-4 sm:p-6 md:p-10 bg-background min-h-screen text-foreground">

        <Head title="Perfil de la Empresa" />

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
                        <p class="font-semibold text-foreground">Perfil Actualizado</p>
                        <p class="mt-1 text-sm text-muted-foreground">Los datos de tu empresa se guardaron
                            correctamente.</p>
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

        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <Link :href="dashboardUrl">
                    <Button variant="outline"
                        class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        <span class="hidden sm:inline">Volver al Dashboard</span>
                        <span class="sm:hidden">Volver</span>
                    </Button>
                </Link>
            </div>

            <div
                class="relative overflow-hidden rounded-2xl mb-6 border border-border bg-gradient-to-br from-primary/10 via-primary/5 to-transparent">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/10 rounded-full blur-[60px]"></div>
                <div class="relative p-6 flex items-center gap-5">
                    <div
                        :class="['flex', 'h-16', 'w-16', 'shrink-0', 'items-center', 'justify-center', 'rounded-2xl', 'border', 'bg-gradient-to-br', avatarConfig.gradient]">
                        <span :class="['text-2xl', 'font-bold', avatarConfig.text]">
                            {{ props.empresa.nombreEmpresa.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">{{ props.empresa.nombreEmpresa }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <Briefcase class="w-4 h-4 text-muted-foreground" />
                            <p class="text-sm text-muted-foreground">{{ props.empresa.tipoEmpresa }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <Card class="border-border overflow-hidden">
                <form @submit.prevent="submit">
                    <CardHeader class="border-b border-border bg-muted/20">
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="w-5 h-5 text-primary" />
                            Información General
                        </CardTitle>
                        <CardDescription>Estos datos aparecerán en tus publicaciones y en el mapa.</CardDescription>
                    </CardHeader>

                    <CardContent class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label for="nombreEmpresa" class="text-muted-foreground mb-2 block">Nombre de la
                                    Empresa</Label>
                                <Input id="nombreEmpresa" v-model="form.nombreEmpresa"
                                    class="bg-background border-border" />
                                <p v-if="form.errors.nombreEmpresa" class="text-destructive text-sm mt-1">{{
                                    form.errors.nombreEmpresa }}</p>
                            </div>
                            <div>
                                <Label for="tipoEmpresa" class="text-muted-foreground mb-2 block">Tipo de
                                    Empresa</Label>
                                <Input id="tipoEmpresa" v-model="form.tipoEmpresa"
                                    placeholder="Ej: Recicladora, Generadora" class="bg-background border-border" />
                                <p v-if="form.errors.tipoEmpresa" class="text-destructive text-sm mt-1">{{
                                    form.errors.tipoEmpresa }}</p>
                            </div>
                        </div>

                        <div>
                            <Label for="direccion" class="text-muted-foreground mb-2 block">Dirección</Label>
                            <Input id="direccion" v-model="form.direccion" placeholder="Calle 123 #45-67"
                                class="bg-background border-border" />
                            <p v-if="form.errors.direccion" class="text-destructive text-sm mt-1">{{
                                form.errors.direccion }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label for="email" class="text-muted-foreground mb-2 flex items-center gap-1">
                                    <Mail class="w-3.5 h-3.5" /> Email Corporativo
                                </Label>
                                <Input id="email" type="email" v-model="form.email"
                                    class="bg-background border-border" />
                                <p v-if="form.errors.email" class="text-destructive text-sm mt-1">{{ form.errors.email
                                    }}</p>
                            </div>
                            <div>
                                <Label for="telefono" class="text-muted-foreground mb-2 flex items-center gap-1">
                                    <Phone class="w-3.5 h-3.5" /> Teléfono
                                </Label>
                                <Input id="telefono" v-model="form.telefono" placeholder="3001234567"
                                    class="bg-background border-border" />
                                <p v-if="form.errors.telefono" class="text-destructive text-sm mt-1">{{
                                    form.errors.telefono }}</p>
                            </div>
                        </div>
                    </CardContent>

                    <CardHeader class="border-y border-border bg-muted/20">
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="w-5 h-5 text-primary" />
                            Ubicación y Cobertura
                        </CardTitle>
                        <CardDescription>Define dónde está tu empresa y qué tan lejos puedes operar.</CardDescription>
                    </CardHeader>

                    <CardContent class="p-6 space-y-5">
                        <div class="flex items-center justify-between gap-3 p-4 rounded-xl border" :class="tieneUbicacion
                            ? 'border-emerald-500/20 bg-emerald-500/5'
                            : 'border-amber-500/20 bg-amber-500/5'">
                            <div class="flex items-center gap-3">
                                <div :class="['flex', 'h-10', 'w-10', 'shrink-0', 'items-center', 'justify-center', 'rounded-full',
                                    tieneUbicacion ? 'bg-emerald-500/15' : 'bg-amber-500/15']">
                                    <MapPin v-if="!tieneUbicacion" class="w-5 h-5 text-amber-500" />
                                    <CheckCircle2 v-else class="w-5 h-5 text-emerald-500" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium"
                                        :class="tieneUbicacion ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                                        {{ tieneUbicacion ? 'Ubicación configurada' : 'Sin ubicación' }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ tieneUbicacion
                                            ? `Lat: ${form.latitud}, Lng: ${form.longitud}`
                                            : 'Usa el botón o ingresa coordenadas manualmente' }}
                                    </p>
                                </div>
                            </div>
                            <Button type="button" @click="obtenerUbicacion" :disabled="ubicacionLoading" :class="tieneUbicacion
                                ? 'border-emerald-500/30 text-emerald-600 hover:bg-emerald-500/10'
                                : 'bg-emerald-600 hover:bg-emerald-700 text-white border-0'" class="shrink-0"
                                variant="outline" size="sm">
                                <Crosshair class="w-4 h-4 mr-2" :class="ubicacionLoading ? 'animate-spin' : ''" />
                                {{ ubicacionLoading ? 'Buscando...' : 'Obtener GPS' }}
                            </Button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <Label for="latitud"
                                    class="text-xs text-muted-foreground uppercase tracking-wide">Latitud</Label>
                                <Input id="latitud" type="number" step="any" v-model.number="form.latitud"
                                    placeholder="4.7110" class="bg-background border-border h-11" />
                                <p v-if="form.errors.latitud" class="text-destructive text-xs">{{ form.errors.latitud }}
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <Label for="longitud"
                                    class="text-xs text-muted-foreground uppercase tracking-wide">Longitud</Label>
                                <Input id="longitud" type="number" step="any" v-model.number="form.longitud"
                                    placeholder="-74.0721" class="bg-background border-border h-11" />
                                <p v-if="form.errors.longitud" class="text-destructive text-xs">{{ form.errors.longitud
                                    }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <Label for="radioOperacion"
                                    class="text-xs text-muted-foreground uppercase tracking-wide flex items-center gap-1">
                                    <Radio class="w-3 h-3" /> Radio (km)
                                </Label>
                                <Input id="radioOperacion" type="number" v-model.number="form.radioOperacion"
                                    class="bg-background border-border h-11" />
                                <p v-if="form.errors.radioOperacion" class="text-destructive text-xs">{{
                                    form.errors.radioOperacion }}</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-muted/30 border border-border/50">
                            <div class="flex items-center gap-4">
                                <div class="relative shrink-0">
                                    <div
                                        class="w-16 h-16 rounded-full border-2 border-dashed border-primary/40 flex items-center justify-center">
                                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                                    </div>
                                    <span
                                        class="absolute -bottom-1 left-1/2 -translate-x-1/2 text-[10px] font-bold text-primary bg-background px-1.5 rounded">
                                        {{ form.radioOperacion || 0 }}km
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-foreground">Área de cobertura</p>
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        Tu empresa puede buscar materiales dentro de un radio de
                                        <strong class="text-foreground">{{ form.radioOperacion || 0 }} km</strong>
                                        desde tu ubicación. Al buscar materiales, este será el radio inicial.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter
                        class="flex flex-col-reverse sm:flex-row justify-between gap-3 border-t border-border bg-muted/20 p-6">
                        <Link :href="dashboardUrl">
                            <Button type="button" variant="outline"
                                class="border-border text-muted-foreground hover:bg-accent hover:text-foreground w-full sm:w-auto">
                                <X class="w-4 h-4 mr-2 text-destructive" />
                                Cancelar
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing"
                            class="bg-primary hover:bg-primary/90 text-primary-foreground w-full sm:w-auto">
                            <Save class="w-4 h-4 mr-2" />
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
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