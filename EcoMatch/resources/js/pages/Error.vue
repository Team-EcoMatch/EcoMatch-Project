<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ShieldAlert, Home, Leaf } from 'lucide-vue-next';

defineProps<{
    status: number;
}>();

const titles: Record<number, string> = {
    403: 'Acceso Denegado',
    404: 'Página no encontrada',
    500: 'Error del servidor'
};

const descriptions: Record<number, string> = {
    403: 'Lo sentimos, no tienes los permisos necesarios para acceder a esta página. Si crees que es un error, contacta al administrador de tu empresa.',
    404: 'Lo sentimos, la página que estás buscando no existe o fue movida a otra dirección.',
    500: 'Ocurrió un error inesperado en el servidor. Por favor, inténtalo de nuevo más tarde.'
};
</script>

<template>
    <div class="min-h-screen bg-background flex flex-col items-center justify-center text-center p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px] -translate-x-1/3 -translate-y-1/3"></div>

        <div class="relative z-10 max-w-md">

            <div class="flex justify-center mb-6">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-red-500/10 ring-1 ring-red-500/20">
                    <ShieldAlert class="h-10 w-10 text-red-500"/>
                </div>
            </div>

            <h1 class="text-7xl font-extrabold tracking-tight text-foreground mb-2">
                {{ status }}
            </h1>

            <h2 class="text-2xl font-bold text-foreground mb-4">
                {{ titles[status] || 'Error Inesperado' }}
            </h2>

            <p class="text-muted-foreground mb-8 leading-7">
                {{ descriptions[status] || 'Ocurrió un error inesperado.' }}
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <Link href="/">
                    <Button size="lg" variant="outline" class="border-border text-foreground hover:bg-accent w-full sm:w-auto">
                        <Leaf class="mr-2 h-4 w-4" />
                        Ir al Inicio
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>