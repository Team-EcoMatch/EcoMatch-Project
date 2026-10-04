<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Leaf, ArrowLeft, Recycle, Mail, CheckCircle2, KeyRound } from 'lucide-vue-next';

defineProps<{ status?: string }>();

const form = useForm({ email: '' });

const submit = () => {
    form.post('/forgot-password', {
        onFinish: () => form.reset('email'),
    });
};
</script>

<template>

    <Head title="Olvidé mi contraseña" />

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
                            Recuperación de cuenta
                        </div>

                        <h1 class="text-4xl xl:text-5xl font-extrabold leading-[1.1] tracking-tight">
                            ¿Olvidaste tu <span class="text-primary">contraseña</span>?
                        </h1>

                        <p class="mt-6 max-w-lg text-lg leading-8 text-muted-foreground">
                            No te preocupes. Ingresa tu correo y te enviaremos un enlace para que puedas volver a
                            acceder a tu cuenta.
                        </p>

                        <div class="mt-10 grid grid-cols-3 gap-4">
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <Mail class="h-5 w-5 text-primary mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Recibe el enlace</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <KeyRound class="h-5 w-5 text-emerald-500 mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Nueva contraseña</p>
                            </div>
                            <div class="rounded-xl border border-border/40 bg-card/30 backdrop-blur-md p-3 text-center">
                                <CheckCircle2 class="h-5 w-5 text-blue-500 mx-auto mb-1" />
                                <p class="text-xs text-muted-foreground">Vuelve a entrar</p>
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
                                <h2 class="text-3xl font-bold tracking-tight text-foreground">Recuperar acceso</h2>
                                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                    Ingresa tu correo electrónico y te enviaremos un enlace de recuperación.
                                </p>
                            </div>

                            <div v-if="status"
                                class="mb-5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                                <CheckCircle2 class="h-4 w-4 shrink-0" />
                                {{ status }}
                            </div>

                            <form @submit.prevent="submit" class="space-y-5">
                                <div class="space-y-2">
                                    <Label for="email" class="text-sm font-medium text-foreground">Correo
                                        electrónico</Label>
                                    <Input id="email" type="email" v-model="form.email" required autofocus
                                        placeholder="admin@empresa.com"
                                        class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary" />
                                    <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}
                                    </p>
                                </div>

                                <Button type="submit"
                                    class="h-12 w-full rounded-lg shadow-lg shadow-primary/20 transition-all duration-200 bg-primary hover:bg-primary/90 text-primary-foreground"
                                    :disabled="form.processing">
                                    <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                    <span>Enviar enlace</span>
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