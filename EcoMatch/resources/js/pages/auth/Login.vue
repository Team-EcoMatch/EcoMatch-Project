<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Loader2, Leaf, Recycle, ArrowRight, Eye, EyeOff, CheckCircle2, X } from 'lucide-vue-next';
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

watch(() => page.props.message, (newMessage) => {
    if (newMessage) {
        notificationMessage.value = newMessage as string;
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 3000);
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

            <section class="relative hidden min-h-screen overflow-hidden bg-background lg:flex lg:flex-col lg:justify-between">
                <div class="absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-primary/10 blur-[120px]"></div>
                <div class="absolute -right-20 bottom-0 h-[500px] w-[500px] rounded-full bg-primary/10 blur-[140px]"></div>
                <div class="absolute right-20 top-20 h-32 w-32 rounded-full border border-primary/10"></div>
                <div class="absolute right-32 top-32 h-16 w-16 rounded-full border border-primary/10"></div>

                <div class="relative z-10 flex min-h-screen flex-col justify-between p-12 xl:p-16">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 ring-1 ring-primary/20">
                            <Leaf class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight">EcoMatch</h2>
                            <p class="text-xs text-muted-foreground">Economía circular</p>
                        </div>
                    </div>

                    <div class="max-w-xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-2 text-sm text-primary">
                            <Recycle class="h-4 w-4" />
                            Conectando empresas
                        </div>

                        <h1 class="text-5xl font-extrabold leading-[1.08] tracking-tight xl:text-6xl">
                            Conecta empresas para construir un
                            <span class="text-primary">futuro sostenible.</span>
                        </h1>

                        <p class="mt-7 max-w-lg text-lg leading-8 text-muted-foreground">
                            Intercambia materiales, reduce residuos y maximiza el valor de tus recursos mediante nuestra plataforma de economía circular.
                        </p>

                        <div class="mt-10 grid grid-cols-3 gap-6">
                            <div>
                                <p class="text-2xl font-bold text-foreground">100%</p>
                                <p class="mt-1 text-sm text-muted-foreground">Economía circular</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-foreground">♻</p>
                                <p class="mt-1 text-sm text-muted-foreground">Menos residuos</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-foreground">∞</p>
                                <p class="mt-1 text-sm text-muted-foreground">Nuevas oportunidades</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        © 2026 EcoMatch. Todos los derechos reservados.
                    </div>
                </div>
            </section>

            <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-muted/20 px-6 py-10 sm:px-10">
                <div class="absolute right-0 top-0 h-96 w-96 rounded-full bg-primary/5 blur-[100px]"></div>
                <div class="absolute bottom-0 left-0 h-72 w-72 rounded-full bg-primary/5 blur-[100px]"></div>

                <div class="absolute left-6 top-6 flex items-center gap-2 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <Leaf class="h-5 w-5 text-primary" />
                    </div>
                    <span class="text-xl font-bold">EcoMatch</span>
                </div>

                <Transition enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-[-10px] scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-[-10px] scale-95">
                    <div v-if="showNotification" class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                        <div class="flex items-start gap-3 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-500/10">
                                <CheckCircle2 class="h-5 w-5 text-green-500" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-foreground">Acción completada</p>
                                <p class="mt-1 text-sm text-muted-foreground">{{ notificationMessage }}</p>
                            </div>
                            <button type="button" @click="showNotification = false" class="text-muted-foreground hover:text-foreground transition-colors">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div class="h-1 bg-muted">
                            <div class="h-full bg-green-500 animate-[toast-progress_3s_linear_forwards]"></div>
                        </div>
                    </div>
                </Transition>

                <div class="relative z-10 w-full max-w-md">
                    <div class="rounded-2xl border border-border bg-card/90 p-7 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-9">

                        <div class="mb-8">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                                <Leaf class="h-6 w-6 text-primary" />
                            </div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Bienvenido de nuevo</h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                Ingresa tus credenciales para acceder a tu cuenta de EcoMatch.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">

                            <div class="space-y-2">
                                <Label for="email" class="text-sm font-medium text-foreground">
                                    Correo electrónico
                                </Label>
                                <Input id="email" type="email" v-model="form.email" required autofocus
                                    autocomplete="username" placeholder="admin@test.com"
                                    class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                <p v-if="form.errors.email" class="text-xs text-destructive">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label for="password" class="text-sm font-medium text-foreground">
                                        Contraseña
                                    </Label>
                                    <Link href="/forgot-password"
                                        class="text-xs font-medium text-primary transition hover:text-primary/80">
                                        ¿Olvidaste tu contraseña?
                                    </Link>
                                </div>
                                <div class="relative">
                                    <Input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password" required
                                        autocomplete="current-password" placeholder="••••••••"
                                        class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring pr-10" />
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                        <EyeOff v-if="showPassword" class="h-5 w-5" />
                                        <Eye v-else class="h-5 w-5" />
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="text-xs text-destructive">
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3 py-1">
                                <Checkbox id="remember" v-model:checked="form.remember" />
                                <Label for="remember" class="cursor-pointer text-sm font-normal text-muted-foreground">
                                    Recordar mi sesión
                                </Label>
                            </div>

                            <Button type="submit"
                                class="h-12 w-full rounded-lg shadow-lg transition-all duration-200"
                                :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <span>{{ form.processing ? 'Ingresando...' : 'Ingresar' }}</span>
                                <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                            </Button>

                        </form>

                        <div class="mt-7 border-t border-border pt-6 text-center">
                            <p class="text-sm text-muted-foreground">¿No tienes una cuenta?</p>
                            <Link href="/register" class="mt-1 inline-block text-sm font-medium text-primary hover:underline">
                                Registra tu empresa aquí
                            </Link>
                        </div>

                    </div>

                    <p class="mt-6 text-center text-xs text-muted-foreground">
                        Al ingresar, aceptas nuestras condiciones de uso y políticas de privacidad.
                    </p>
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