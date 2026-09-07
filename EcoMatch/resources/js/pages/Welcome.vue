<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Leaf, Recycle, MapPin, ArrowRight, ShieldCheck, Users, Building2, PackageCheck, Handshake } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();

const slides = [
    { 
        icon: MapPin, 
        title: "Geolocalización Inteligente", 
        desc: "Encuentra materiales disponibles en un mapa interactivo y calcula distancias exactas para optimizar tu logística de intercambio." 
    },
    { 
        icon: Users, 
        title: "Gestión de Roles", 
        desc: "Administra tu equipo con roles de Jefe y Empleado. Controla quién publica, quién aprueba y mantiene la seguridad de tu empresa." 
    },
    { 
        icon: Handshake, 
        title: "Negociación Directa", 
        desc: "Envía solicitudes de intercambio con mensajes personalizados a otras empresas y gestiona tus acuerdos en una bandeja centralizada." 
    },
    { 
        icon: PackageCheck, 
        title: "Publicación en Segundos", 
        desc: "Sube imágenes, define cantidades y unidades de medida. Tus materiales estarán listos para ser descubiertos en menos de un minuto." 
    }
];

const currentSlide = ref(0);
let interval: any;

onMounted(() => {
    interval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 4500);
});

onUnmounted(() => clearInterval(interval));
</script>

<template>
    <Head title="EcoMatch - Economía Circular" />

    <div class="min-h-screen bg-background text-foreground overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[120px] translate-x-1/3 translate-y-1/3"></div>

        <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-background/70 border-b border-border/40">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 ring-1 ring-primary/20">
                        <Leaf class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight">EcoMatch</h2>
                    </div>
                </div>

                <nav class="flex items-center gap-3">
                    <Link href="/login">
                        <Button variant="ghost" class="text-foreground hover:bg-accent">
                            Iniciar Sesión
                        </Button>
                    </Link>
                    <Link href="/register">
                        <Button class="bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg">
                            Registrar Empresa
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>
                    </Link>
                </nav>
            </div>
        </header>

        <main class="relative z-10 max-w-7xl mx-auto px-6 pt-40 pb-20 flex flex-col items-center text-center">
            
            <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-2 text-sm text-primary mb-8">
                <Recycle class="h-4 w-4" />
                Plataforma de Intercambio de Materiales
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-[1.05] mb-6">
                Conectando empresas para un<br>
                <span class="text-primary">futuro sostenible</span>
            </h1>

            <p class="max-w-2xl text-lg md:text-xl text-muted-foreground leading-8 mb-10">
                Reduce tus residuos industriales y maximiza el valor de tus materiales. Únete a la red de empresas que están transformando la economía circular.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <Link href="/register">
                    <Button size="lg" class="h-14 px-8 text-base bg-primary text-primary-foreground hover:bg-primary/90 shadow-xl">
                        Comenzar Gratis
                        <ArrowRight class="ml-2 h-5 w-5" />
                    </Button>
                </Link>
                <Link href="/login">
                    <Button size="lg" variant="outline" class="h-14 px-8 text-base border-border text-foreground hover:bg-accent">
                        Ya tengo cuenta
                    </Button>
                </Link>
            </div>
        </main>

        <section class="relative z-10 max-w-4xl mx-auto px-6 py-16">
            <div class="relative h-72 max-w-2xl mx-auto">
                <Transition mode="out-in" enter-active-class="transition-all duration-700 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-300 ease-in absolute inset-0" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                    <div :key="currentSlide" class="bg-card border border-border rounded-2xl p-10 shadow-xl flex flex-col items-center text-center h-full justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 mb-6 ring-1 ring-primary/20">
                            <component :is="slides[currentSlide].icon" class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-foreground">{{ slides[currentSlide].title }}</h3>
                        <p class="text-muted-foreground leading-7 max-w-md">{{ slides[currentSlide].desc }}</p>
                    </div>
                </Transition>
            </div>

            <div class="flex justify-center gap-2 mt-8">
                <button v-for="(slide, index) in slides" :key="index" @click="currentSlide = index" class="h-2 rounded-full transition-all duration-300" :class="currentSlide === index ? 'w-8 bg-primary' : 'w-2 bg-muted hover:bg-primary/50'"></button>
            </div>
        </section>

        <section class="relative z-10 max-w-7xl mx-auto px-6 py-24">
            <h2 class="text-4xl font-bold text-center mb-16">¿Cómo funciona <span class="text-primary">EcoMatch</span>?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 mb-6 ring-1 ring-primary/20">
                        <Building2 class="h-8 w-8 text-primary" />
                    </div>
                    <h3 class="text-2xl font-bold mb-3">1. Registra tu Empresa</h3>
                    <p class="text-muted-foreground leading-7 max-w-xs">Crea la cuenta de tu empresa, completa tu perfil y configura tus categorías de materiales en minutos.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 mb-6 ring-1 ring-primary/20">
                        <PackageCheck class="h-8 w-8 text-primary" />
                    </div>
                    <h3 class="text-2xl font-bold mb-3">2. Publica Materiales</h3>
                    <p class="text-muted-foreground leading-7 max-w-xs">Sube los materiales de desecho que tienes disponibles. El jefe aprueba la publicación y queda visible para todos.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 mb-6 ring-1 ring-primary/20">
                        <Handshake class="h-8 w-8 text-primary" />
                    </div>
                    <h3 class="text-2xl font-bold mb-3">3. Intercambia</h3>
                    <p class="text-muted-foreground leading-7 max-w-xs">Explora el mapa, encuentra lo que necesitas, envía una solicitud y haz el intercambio con otras empresas.</p>
                </div>
            </div>
        </section>

        <section class="relative z-10 max-w-4xl mx-auto px-6 py-24 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">¿Listo para empezar?</h2>
            <p class="text-lg text-muted-foreground mb-10 max-w-xl mx-auto">Únete hoy a la plataforma líder en economía circular y transforma los residuos de tu empresa.</p>
            <Link href="/register">
                <Button size="lg" class="h-14 px-8 text-base bg-primary text-primary-foreground hover:bg-primary/90 shadow-xl">
                    Registrar mi Empresa Gratis
                    <ArrowRight class="ml-2 h-5 w-5" />
                </Button>
            </Link>
        </section>

        <footer class="relative z-10 border-t border-border/40 mt-10">
            <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Leaf class="h-6 w-6 text-primary" />
                    <span class="font-bold text-lg">EcoMatch</span>
                </div>
                <p class="text-sm text-muted-foreground">
                    © 2026 EcoMatch. Todos los derechos reservados.
                </p>
            </div>
        </footer>

    </div>
</template>