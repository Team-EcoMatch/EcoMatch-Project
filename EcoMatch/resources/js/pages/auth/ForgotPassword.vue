<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Leaf, ArrowLeft } from 'lucide-vue-next';

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

            <section class="relative hidden min-h-screen overflow-hidden bg-background lg:flex lg:flex-col lg:justify-between">
                <div class="absolute -left-32 top-1/4 h-96 w-96 rounded-full bg-primary/10 blur-[120px]"></div>
                <div class="absolute -right-20 bottom-0 h-[500px] w-[500px] rounded-full bg-primary/10 blur-[140px]"></div>
                
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
                        <h1 class="text-5xl font-extrabold leading-[1.08] tracking-tight xl:text-6xl">
                            ¿Olvidaste tu <span class="text-primary">contraseña</span>?
                        </h1>
                        <p class="mt-7 max-w-lg text-lg leading-8 text-muted-foreground">
                            No te preocupes. Ingresa tu correo y te enviaremos un enlace para que puedas volver a acceder a tu cuenta.
                        </p>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        © 2026 EcoMatch. Todos los derechos reservados.
                    </div>
                </div>
            </section>

            <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-muted/20 px-6 py-10 sm:px-10">
                <div class="absolute right-0 top-0 h-96 w-96 rounded-full bg-primary/5 blur-[100px]"></div>
                
                <div class="relative z-10 w-full max-w-md">
                    <div class="rounded-2xl border border-border bg-card/90 p-7 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-9">
                        
                        <div class="mb-8">
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                                <Leaf class="h-6 w-6 text-primary" />
                            </div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Recuperar acceso</h2>
                            <p class="mt-2 text-sm leading-6 text-muted-foreground">
                                Ingresa tu correo electrónico y te enviaremos un enlace de recuperación.
                            </p>
                        </div>

                        <div v-if="status" class="mb-5 rounded-lg border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary">
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="space-y-2">
                                <Label for="email" class="text-sm font-medium text-foreground">Correo electrónico</Label>
                                <Input id="email" type="email" v-model="form.email" required autofocus placeholder="admin@empresa.com" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                            </div>

                            <Button type="submit" class="h-12 w-full rounded-lg shadow-lg transition-all duration-200" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <span>Enviar enlace</span>
                            </Button>
                        </form>

                        <div class="mt-7 border-t border-border pt-6 text-center">
                            <Link href="/login" class="inline-flex items-center text-sm font-medium text-primary hover:underline">
                                <ArrowLeft class="w-4 h-4 mr-2" />
                                Volver a iniciar sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>