<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Leaf, ArrowRight, Eye, EyeOff, CheckCircle2, X, ArrowLeft } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

const page = usePage();

const form = useForm({
    nombreEmpresa: '',
    direccion: '',
    telefono: '',
    tipoEmpresa: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const passwordMismatch = computed(() => {
    return form.password !== form.password_confirmation && form.password_confirmation.length > 0;
});

const showNotification = ref(false);
const notificationMessage = ref('');

watch(() => page.props.message, (newMessage) => {
    if (newMessage) {
        notificationMessage.value = newMessage as string;
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 3000);
    }
}, { immediate: true });

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Registrar Empresa" />

    <div class="fixed inset-0 z-50 bg-background text-foreground overflow-auto">
        <div class="grid min-h-full w-full lg:grid-cols-2">

            <section
                class="relative hidden min-h-screen overflow-hidden bg-background lg:flex lg:flex-col lg:justify-between">
                <div class="absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-primary/10 blur-[120px]"></div>
                <div class="absolute -right-20 bottom-0 h-[500px] w-[500px] rounded-full bg-primary/10 blur-[140px]">
                </div>

                <div class="relative z-10 flex min-h-screen flex-col justify-between p-12 xl:p-16">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 ring-1 ring-primary/20">
                            <Leaf class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight">EcoMatch</h2>
                            <p class="text-xs text-muted-foreground">Economía circular</p>
                        </div>
                    </div>

                    <div class="max-w-xl">
                        <h1 class="text-5xl font-extrabold leading-[1.08] tracking-tight xl:text-6xl">
                            Únete a la red de <span class="text-primary">economía circular</span> más grande.
                        </h1>
                        <p class="mt-7 max-w-lg text-lg leading-8 text-muted-foreground">
                            Registra tu empresa, publica tus materiales de desecho y comienza a intercambiar con otras
                            empresas hoy mismo.
                        </p>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        © 2026 EcoMatch. Todos los derechos reservados.
                    </div>
                </div>
            </section>

            <section class="relative flex min-h-screen flex-col overflow-hidden bg-muted/20">

                <div class="flex justify-between items-center w-full p-4 sm:p-6 z-20">
                    <div class="flex items-center gap-2 lg:hidden">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                            <Leaf class="h-5 w-5 text-primary" />
                        </div>
                        <span class="text-xl font-bold">EcoMatch</span>
                    </div>

                    <div class="hidden lg:block"></div>

                    <Link href="/"
                        class="group inline-flex items-center gap-2 px-4 py-2 bg-card/60 backdrop-blur-md border border-border/60 rounded-full shadow-lg text-sm font-medium text-foreground hover:bg-card/90 hover:border-primary/50 hover:text-primary transition-all duration-300">
                        <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                        Inicio
                    </Link>
                </div>

                <div class="flex flex-1 items-center justify-center px-6 pb-10 sm:px-10">

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

                    <div class="relative z-10 w-full max-w-md">
                        <div
                            class="rounded-2xl border border-border bg-card/90 p-7 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-9">

                            <div class="mb-8">
                                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                                    <Leaf class="h-6 w-6 text-primary" />
                                </div>
                                <h2 class="text-3xl font-bold tracking-tight text-foreground">Crear cuenta</h2>
                                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                    Completa los datos de tu empresa y del administrador.
                                </p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-5">

                                <div class="space-y-4 border-b border-border pb-5">
                                    <h3 class="text-sm font-semibold text-foreground">Datos de la Empresa</h3>

                                    <div>
                                        <Label for="nombreEmpresa" class="text-sm font-medium text-foreground">Nombre de
                                            la Empresa</Label>
                                        <Input id="nombreEmpresa" type="text" v-model="form.nombreEmpresa" required
                                            autofocus placeholder="Mi Empresa Recicladora"
                                            class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.nombreEmpresa" class="text-xs text-destructive mt-1">{{
                                            form.errors.nombreEmpresa }}</p>
                                    </div>

                                    <div>
                                        <Label for="direccion"
                                            class="text-sm font-medium text-foreground">Dirección</Label>
                                        <Input id="direccion" type="text" v-model="form.direccion" required
                                            placeholder="Calle 123 #45-67"
                                            class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.direccion" class="text-xs text-destructive mt-1">{{
                                            form.errors.direccion }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                        <div>
                                            <Label for="telefono"
                                                class="text-sm font-medium text-foreground">Teléfono</Label>
                                            <Input id="telefono" type="tel" v-model="form.telefono" required
                                                placeholder="3001234567"
                                                class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                            <p v-if="form.errors.telefono" class="text-xs text-destructive mt-1">{{
                                                form.errors.telefono }}</p>
                                        </div>
                                        <div>
                                            <Label for="tipoEmpresa" class="text-sm font-medium text-foreground">Tipo de
                                                Empresa</Label>
                                            <Input id="tipoEmpresa" type="text" v-model="form.tipoEmpresa" required
                                                placeholder="Recicladora, Generadora..."
                                                class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                            <p v-if="form.errors.tipoEmpresa" class="text-xs text-destructive mt-1">{{
                                                form.errors.tipoEmpresa }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h3 class="text-sm font-semibold text-foreground">Datos del Administrador</h3>

                                    <div>
                                        <Label for="name" class="text-sm font-medium text-foreground">Tu Nombre</Label>
                                        <Input id="name" type="text" v-model="form.name" required
                                            placeholder="Juan Pérez"
                                            class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.name" class="text-xs text-destructive mt-1">{{
                                            form.errors.name }}</p>
                                    </div>

                                    <div>
                                        <Label for="email" class="text-sm font-medium text-foreground">Correo
                                            Electrónico</Label>
                                        <Input id="email" type="email" v-model="form.email" required
                                            placeholder="admin@empresa.com"
                                            class="h-12 mt-1 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.email" class="text-xs text-destructive mt-1">{{
                                            form.errors.email }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                        <div>
                                            <Label for="password"
                                                class="text-sm font-medium text-foreground">Contraseña</Label>
                                            <div class="relative mt-1">
                                                <Input id="password" :type="showPassword ? 'text' : 'password'"
                                                    v-model="form.password" required placeholder="••••••••"
                                                    class="h-12 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring pr-10" />
                                                <button type="button" @click="showPassword = !showPassword"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                    <EyeOff v-if="showPassword" class="h-5 w-5" />
                                                    <Eye v-else class="h-5 w-5" />
                                                </button>
                                            </div>
                                            <p v-if="form.errors.password" class="text-xs text-destructive mt-1">{{
                                                form.errors.password }}</p>
                                        </div>
                                        <div>
                                            <Label for="password_confirmation"
                                                class="text-sm font-medium text-foreground">Confirmar</Label>
                                            <div class="relative mt-1">
                                                <Input id="password_confirmation"
                                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                                    v-model="form.password_confirmation" required placeholder="••••••••"
                                                    class="h-12 block w-full rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring pr-10" />
                                                <button type="button"
                                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                    <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                                                    <Eye v-else class="h-5 w-5" />
                                                </button>
                                            </div>
                                            <p v-if="passwordMismatch" class="text-xs text-destructive mt-1">
                                                Las contraseñas no coinciden.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <Button type="submit"
                                    class="h-12 w-full rounded-lg shadow-lg transition-all duration-200"
                                    :disabled="form.processing">
                                    <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                    <span>{{ form.processing ? 'Creando cuenta...' : 'Registrar Empresa' }}</span>
                                    <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                                </Button>
                            </form>

                            <div class="mt-7 border-t border-border pt-6 text-center">
                                <p class="text-sm text-muted-foreground">¿Ya tienes una cuenta?</p>
                                <Link href="/login"
                                    class="mt-1 inline-block text-sm font-medium text-primary hover:underline">
                                    Inicia sesión aquí
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section> 
        </div>
    </div>
</template>

<style>
@keyframes toast-progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>