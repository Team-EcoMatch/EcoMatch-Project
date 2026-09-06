<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Leaf, ArrowRight, Eye, EyeOff } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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
                            Únete a la red de <span class="text-primary">economía circular</span> más grande.
                        </h1>
                        <p class="mt-7 max-w-lg text-lg leading-8 text-muted-foreground">
                            Registra tu empresa, publica tus materiales de desecho y comienza a intercambiar con otras empresas hoy mismo.
                        </p>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        © 2026 EcoMatch. Todos los derechos reservados.
                    </div>
                </div>
            </section>

            <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-muted/20 px-6 py-10 sm:px-10">
                <div class="absolute right-0 top-0 h-96 w-96 rounded-full bg-primary/5 blur-[100px]"></div>
                
                <div class="absolute left-6 top-6 flex items-center gap-2 lg:hidden">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <Leaf class="h-5 w-5 text-primary" />
                    </div>
                    <span class="text-xl font-bold">EcoMatch</span>
                </div>

                <div class="relative z-10 w-full max-w-md">
                    <div class="rounded-2xl border border-border bg-card/90 p-7 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-9">
                        
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

                                <div class="space-y-2">
                                    <Label for="nombreEmpresa" class="text-sm font-medium text-foreground">Nombre de la Empresa</Label>
                                    <Input id="nombreEmpresa" type="text" v-model="form.nombreEmpresa" required autofocus placeholder="Mi Empresa Recicladora" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                    <p v-if="form.errors.nombreEmpresa" class="text-xs text-destructive">{{ form.errors.nombreEmpresa }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="direccion" class="text-sm font-medium text-foreground">Dirección</Label>
                                    <Input id="direccion" type="text" v-model="form.direccion" required placeholder="Calle 123 #45-67" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                    <p v-if="form.errors.direccion" class="text-xs text-destructive">{{ form.errors.direccion }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="telefono" class="text-sm font-medium text-foreground">Teléfono</Label>
                                        <Input id="telefono" type="tel" v-model="form.telefono" required placeholder="3001234567" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.telefono" class="text-xs text-destructive">{{ form.errors.telefono }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="tipoEmpresa" class="text-sm font-medium text-foreground">Tipo de Empresa</Label>
                                        <Input id="tipoEmpresa" type="text" v-model="form.tipoEmpresa" required placeholder="Recicladora, Generadora..." class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                        <p v-if="form.errors.tipoEmpresa" class="text-xs text-destructive">{{ form.errors.tipoEmpresa }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-sm font-semibold text-foreground">Datos del Administrador</h3>

                                <div class="space-y-2">
                                    <Label for="name" class="text-sm font-medium text-foreground">Tu Nombre</Label>
                                    <Input id="name" type="text" v-model="form.name" required placeholder="Juan Pérez" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                    <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email" class="text-sm font-medium text-foreground">Correo Electrónico</Label>
                                    <Input id="email" type="email" v-model="form.email" required placeholder="admin@empresa.com" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring" />
                                    <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="password" class="text-sm font-medium text-foreground">Contraseña</Label>
                                        <div class="relative">
                                            <Input id="password" :type="showPassword ? 'text' : 'password'" v-model="form.password" required placeholder="••••••••" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring pr-10" />
                                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                <EyeOff v-if="showPassword" class="h-5 w-5" />
                                                <Eye v-else class="h-5 w-5" />
                                            </button>
                                        </div>
                                        <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
                                    </div>
                                                                    <div class="space-y-2">
                                    <Label for="password_confirmation" class="text-sm font-medium text-foreground">Confirmar</Label>
                                    <div class="relative">
                                        <Input id="password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'" v-model="form.password_confirmation" required placeholder="••••••••" class="h-12 rounded-lg border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring pr-10" />
                                        <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                            <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                                            <Eye v-else class="h-5 w-5" />
                                        </button>
                                    </div>
                                    <p v-if="passwordMismatch" class="text-xs text-destructive">
                                        Las contraseñas no coinciden.
                                    </p>
                                </div>
                                </div>
                            </div>

                            <Button type="submit" class="h-12 w-full rounded-lg shadow-lg transition-all duration-200" :disabled="form.processing">
                                <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                <span>{{ form.processing ? 'Creando cuenta...' : 'Registrar Empresa' }}</span>
                                <ArrowRight v-if="!form.processing" class="ml-2 h-4 w-4" />
                            </Button>
                        </form>

                        <div class="mt-7 border-t border-border pt-6 text-center">
                            <p class="text-sm text-muted-foreground">¿Ya tienes una cuenta?</p>
                            <Link href="/login" class="mt-1 inline-block text-sm font-medium text-primary hover:underline">
                                Inicia sesión aquí
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>