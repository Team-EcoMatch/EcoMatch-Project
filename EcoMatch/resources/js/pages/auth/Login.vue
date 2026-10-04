<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Loader2, Leaf, Recycle, ArrowRight, Eye, EyeOff, CheckCircle2, X, XCircle, ArrowLeft, Ban } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const page = usePage();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const showNotification = ref(false);
const notificationMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

watch(() => page.props.message, (newMessage) => {
    if (newMessage) {
        notificationMessage.value = newMessage as string;
        toastType.value = 'success';
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 3000);
    }
}, { immediate: true });

watch(() => form.errors.email, (error) => {
    if (error) {
        notificationMessage.value = error;
        toastType.value = 'error';
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 5000);
    }
}, { immediate: true });

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Iniciar Sesión" />

    <div class="fixed inset-0 z-50 bg-background text-foreground overflow-auto">
        <div class="grid min-h-full w-full lg:grid-cols-2">

            <section
                class="relative hidden min-h-screen overflow-hidden bg-background lg:flex lg:flex-col lg:justify-between">
                <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] bg-primary/10 rounded-full blur-[130px]"
                    style="animation: drift-a 22s ease-in-out infinite;"></div>
                <div class="absolute bottom-[-10%] right-[-5%] w-[400px] h-[400px] bg-emerald-500/8 rounded-full blur-[120px]"
                    style="animation: drift-b 28s ease-in-out infinite;"></div>
                <div class="absolute right-16 top-16 h-28 w-28 rounded-full border border-primary/10"></div>
                <div class="absolute right-24 top-24 h-14 w-14 rounded-full border border-primary/10"></div>
                <div class="absolute left-12 bottom-32 h-20 w-20 rounded-full border border-emerald-500/10"></div>

                <div v-for="n in 8" :key="n" class="absolute rounded-full bg-emerald-400/20" :style="{
                    top: `${10 + (n * 11) % 80}%`,
                    left: `${15 + (n * 17) % 70}%`,
                    width: '3px', height: '3px',
                    animation: `float-dot ${4 + n}s ease-in-out infinite`,
                    animationDelay: `${n * 0.5}s`,
                }"></div>

                <div class="relative z-10 flex min-h-screen flex-col justify-between p-12 xl:p-16">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-emerald-600 shadow-lg shadow-primary/20">
                            <Leaf class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight">EcoMatch</h2>
                            <p class="text-xs text-muted-foreground">Economía circular</p>
                        </div>
                    </div>

                    <div class="max-w-xl">
                        <div
                            class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-2 text-sm text-primary backdrop-blur-md">
                            <Recycle class="h-4 w-4" />
                            Conectando empresas
                        </div>

                        <h1 class="text-4xl xl:text-5xl font-extrabold leading-[1.1] tracking-tight">
                            Conecta empresas para construir un
                            <span class="text-primary">futuro sostenible.</span>
                        </h1>

                        <p class="mt-6 max-w-lg text-lg leading-8 text-muted-foreground">
                            Intercambia materiales, reduce residuos y maximiza el valor de tus recursos mediante nuestra
                            plataforma de economía circular.
                        </p>

                        <div class="mt-10 grid grid-cols-3 gap-4">
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <p class="text-xl font-bold text-primary">100%</p>
                                <p class="mt-1 text-xs text-muted-foreground">Economía circular</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <p class="text-xl font-bold text-emerald-500">♻</p>
                                <p class="mt-1 text-xs text-muted-foreground">Menos residuos</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <p class="text-xl font-bold text-blue-500">∞</p>
                                <p class="mt-1 text-xs text-muted-foreground">Oportunidades</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        © 2026 EcoMatch. Todos los derechos reservados.
                    </div>
                </div>
            </section>

            <section class="relative flex min-h-screen flex-col overflow-hidden bg-muted/20">
                <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-primary/5 rounded-full blur-[100px]"></div>

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
                            class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border bg-card shadow-2xl overflow-hidden"
                            :class="toastType === 'error' ? 'border-red-300 dark:border-red-800' : 'border-border'">
                            <div v-if="toastType === 'error'" class="bg-red-600 p-3 flex items-center gap-2">
                                <Ban class="h-5 w-5 text-white" />
                                <p class="font-semibold text-white">Cuenta desactivada</p>
                                <button type="button" @click="showNotification = false"
                                    class="ml-auto text-white hover:bg-red-700 rounded p-1">
                                    <X class="h-4 w-4" />
                                </button>
                            </div>
                            <div v-else class="flex items-start gap-3 p-4">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/10">
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
                            <div v-if="toastType === 'error'" class="p-4">
                                <p class="text-sm text-muted-foreground">{{ notificationMessage }}</p>
                            </div>
                        </div>
                    </Transition>

                    <div class="relative z-10 w-full max-w-md">
                        <div class="relative">
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary/15 to-emerald-500/10 blur-sm">
                            </div>
                            <div
                                class="relative rounded-2xl border border-border/60 bg-card/90 p-7 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-9">

                                <div class="mb-8">
                                    <div
                                        class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary/15 to-emerald-500/10 border border-primary/15">
                                        <Leaf class="h-6 w-6 text-primary" />
                                    </div>
                                    <h2 class="text-3xl font-bold tracking-tight text-foreground">Bienvenido de nuevo
                                    </h2>
                                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                        Ingresa tus credenciales para acceder a tu cuenta de EcoMatch.
                                    </p>
                                </div>

                                <form @submit.prevent="submit" class="space-y-5">
                                    <div class="space-y-2">
                                        <Label for="email" class="text-sm font-medium text-foreground">Correo
                                            electrónico</Label>
                                        <Input id="email" type="email" v-model="form.email" required autofocus
                                            autocomplete="username" placeholder="admin@test.com"
                                            class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary" />
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <Label for="password"
                                                class="text-sm font-medium text-foreground">Contraseña</Label>
                                            <Link href="/forgot-password"
                                                class="text-xs font-medium text-primary transition hover:text-primary/80">
                                                ¿Olvidaste tu contraseña?
                                            </Link>
                                        </div>
                                        <div class="relative">
                                            <Input id="password" :type="showPassword ? 'text' : 'password'"
                                                v-model="form.password" required autocomplete="current-password"
                                                placeholder="••••••••"
                                                class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary pr-10" />
                                            <button type="button" @click="showPassword = !showPassword"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                <EyeOff v-if="showPassword" class="h-5 w-5" />
                                                <Eye v-else class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 py-1">
                                        <Checkbox id="remember" v-model:checked="form.remember" />
                                        <Label for="remember"
                                            class="cursor-pointer text-sm font-normal text-muted-foreground">
                                            Recordar mi sesión
                                        </Label>
                                    </div>

                                    <Button type="submit"
                                        class="h-12 w-full rounded-lg shadow-lg shadow-primary/20 transition-all duration-200 bg-primary hover:bg-primary/90 text-primary-foreground"
                                        :disabled="form.processing">
                                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                        <span>{{ form.processing ? 'Ingresando...' : 'Ingresar' }}</span>
                                        <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                                    </Button>
                                </form>

                                <div class="mt-7 border-t border-border/50 pt-6 text-center">
                                    <p class="text-sm text-muted-foreground">¿No tienes una cuenta?</p>
                                    <Link href="/register"
                                        class="mt-1 inline-block text-sm font-medium text-primary hover:underline">
                                        Registra tu empresa aquí
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <p class="mt-6 text-center text-xs text-muted-foreground">
                            Al ingresar, aceptas nuestras condiciones de uso y políticas de privacidad.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
@keyframes drift-a {

    0%,
    100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(40px, 20px);
    }
}

@keyframes drift-b {

    0%,
    100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(-30px, -15px);
    }
}

@keyframes float-dot {

    0%,
    100% {
        transform: translateY(0);
        opacity: 0.3;
    }

    50% {
        transform: translateY(-25px);
        opacity: 0.8;
    }
}
</style>