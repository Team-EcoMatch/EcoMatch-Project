<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Leaf, ArrowLeft, Eye, EyeOff, Check, X, ShieldCheck, Recycle, Lock, KeyRound, CheckCircle2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{ email: string; token: string }>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const requisitos = computed(() => [
    { label: 'Mínimo 8 caracteres', cumplido: form.password.length >= 8 },
    { label: 'Una letra mayúscula (A-Z)', cumplido: /[A-Z]/.test(form.password) },
    { label: 'Una letra minúscula (a-z)', cumplido: /[a-z]/.test(form.password) },
    { label: 'Un número (0-9)', cumplido: /[0-9]/.test(form.password) },
    { label: 'Un carácter especial (!@#$%^&*)', cumplido: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(form.password) },
]);

const todosCumplidos = computed(() => requisitos.value.every(r => r.cumplido));

const contrasenasCoinciden = computed(() => {
    return form.password === form.password_confirmation && form.password_confirmation.length > 0;
});

const fuerzaContrasena = computed(() => {
    let puntos = 0;
    if (form.password.length >= 8) puntos++;
    if (form.password.length >= 12) puntos++;
    if (/[a-z]/.test(form.password)) puntos++;
    if (/[A-Z]/.test(form.password)) puntos++;
    if (/[0-9]/.test(form.password)) puntos++;
    if (/[^a-zA-Z0-9]/.test(form.password)) puntos++;

    if (puntos <= 2) return { nivel: 'Débil', color: 'bg-red-500', width: '33%', texto: 'text-red-500' };
    if (puntos <= 4) return { nivel: 'Media', color: 'bg-amber-500', width: '66%', texto: 'text-amber-500' };
    return { nivel: 'Fuerte', color: 'bg-blue-500', width: '100%', texto: 'text-blue-500' };
});

const submit = () => {
    if (!todosCumplidos.value) return;
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Restablecer contraseña" />

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
                            Nueva contraseña
                        </div>

                        <h1 class="text-4xl xl:text-5xl font-extrabold leading-[1.1] tracking-tight">
                            Crea una <span class="text-primary">nueva contraseña</span>.
                        </h1>

                        <p class="mt-6 max-w-lg text-lg leading-8 text-muted-foreground">
                            Elige una contraseña segura para proteger la información de tu empresa en EcoMatch.
                        </p>

                        <div class="mt-10 grid grid-cols-3 gap-4">
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <Lock class="h-5 w-5 text-primary mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Segura</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <ShieldCheck class="h-5 w-5 text-emerald-500 mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Protegida</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <CheckCircle2 class="h-5 w-5 text-blue-500 mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Verificada</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-muted-foreground">© 2026 EcoMatch. Todos los derechos reservados.</div>
                </div>
            </section>

            <section
                class="relative flex min-h-screen items-center justify-center overflow-hidden bg-muted/20 px-6 py-10 sm:px-10">
                <div class="absolute top-0 right-0 w-[300px] h-[300px] bg-primary/5 rounded-full blur-[100px]"></div>

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
                                    <KeyRound class="h-6 w-6 text-primary" />
                                </div>
                                <h2 class="text-3xl font-bold tracking-tight text-foreground">Restablecer contraseña
                                </h2>
                                <p class="mt-2 text-sm leading-6 text-muted-foreground">Ingresa tu nueva contraseña.</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-5">
                                <div class="space-y-2">
                                    <Label for="email" class="text-sm font-medium text-foreground">Correo
                                        electrónico</Label>
                                    <Input id="email" type="email" v-model="form.email" required
                                        placeholder="admin@empresa.com"
                                        class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary" />
                                    <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password" class="text-sm font-medium text-foreground">Nueva
                                        contraseña</Label>
                                    <div class="relative">
                                        <Input id="password" :type="showPassword ? 'text' : 'password'"
                                            v-model="form.password" required placeholder="••••••••"
                                            class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary pr-10" />
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                            <EyeOff v-if="showPassword" class="h-5 w-5" />
                                            <Eye v-else class="h-5 w-5" />
                                        </button>
                                    </div>

                                    <div v-if="form.password.length > 0" class="mt-3 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 flex-1 rounded-full bg-muted overflow-hidden">
                                                <div class="h-full transition-all duration-300"
                                                    :class="fuerzaContrasena.color"
                                                    :style="{ width: fuerzaContrasena.width }"></div>
                                            </div>
                                            <span class="text-xs font-medium" :class="fuerzaContrasena.texto">{{
                                                fuerzaContrasena.nivel }}</span>
                                        </div>

                                        <div class="rounded-lg border border-border bg-muted/30 p-3 space-y-1.5">
                                            <p
                                                class="text-xs font-semibold text-muted-foreground mb-2 flex items-center gap-1.5">
                                                <ShieldCheck class="w-3.5 h-3.5" />
                                                Requisitos de la contraseña:
                                            </p>
                                            <div v-for="req in requisitos" :key="req.label"
                                                class="flex items-center gap-2">
                                                <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full transition-colors"
                                                    :class="req.cumplido ? 'bg-blue-500' : 'bg-muted-foreground/20'">
                                                    <Check v-if="req.cumplido" class="h-2.5 w-2.5 text-white" />
                                                    <X v-else class="h-2.5 w-2.5 text-muted-foreground" />
                                                </div>
                                                <span class="text-xs transition-colors"
                                                    :class="req.cumplido ? 'text-blue-500 font-medium' : 'text-muted-foreground'">
                                                    {{ req.label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <p v-if="form.errors.password" class="text-xs text-destructive">{{
                                        form.errors.password }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation"
                                        class="text-sm font-medium text-foreground">Confirmar contraseña</Label>
                                    <div class="relative">
                                        <Input id="password_confirmation"
                                            :type="showPasswordConfirmation ? 'text' : 'password'"
                                            v-model="form.password_confirmation" required placeholder="••••••••"
                                            class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary pr-10" />
                                        <button type="button"
                                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                            <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                                            <Eye v-else class="h-5 w-5" />
                                        </button>
                                    </div>

                                    <div v-if="form.password_confirmation.length > 0"
                                        class="flex items-center gap-2 mt-2">
                                        <div class="flex h-4 w-4 items-center justify-center rounded-full transition-colors"
                                            :class="contrasenasCoinciden ? 'bg-blue-500' : 'bg-red-500'">
                                            <Check v-if="contrasenasCoinciden" class="h-2.5 w-2.5 text-white" />
                                            <X v-else class="h-2.5 w-2.5 text-white" />
                                        </div>
                                        <span class="text-xs"
                                            :class="contrasenasCoinciden ? 'text-blue-500' : 'text-red-500'">
                                            {{ contrasenasCoinciden ? `Las contraseñas coinciden` : `Las contraseñas no
                                            coinciden` }}
                                        </span>
                                    </div>

                                    <p v-if="form.errors.password_confirmation" class="text-xs text-destructive">{{
                                        form.errors.password_confirmation }}</p>
                                </div>

                                <Button type="submit"
                                    class="h-12 w-full rounded-lg shadow-lg shadow-primary/20 transition-all duration-200 bg-primary hover:bg-primary/90 text-primary-foreground"
                                    :disabled="form.processing || !todosCumplidos || !contrasenasCoinciden">
                                    <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                    <span>Restablecer contraseña</span>
                                </Button>
                            </form>

                            <div class="mt-7 border-t border-border/50 pt-6 text-center">
                                <Link href="/login"
                                    class="inline-flex items-center text-sm font-medium text-primary hover:underline">
                                    <ArrowLeft class="w-4 h-4 mr-2" />
                                    Volver a iniciar sesión
                                </Link>
                            </div>
                        </div>
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