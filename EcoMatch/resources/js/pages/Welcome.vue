<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Leaf, Recycle, MapPin, ArrowRight, ShieldCheck, Users, Building2, PackageCheck, Handshake, Package, TrendingUp, Globe2, MessageSquare, Sparkles } from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';

const props = defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    stats?: {
        empresas: number;
        intercambios: number;
        kgRecuperados: number;
    };
    materialesStats?: { nombre: string; total: number }[];
}>();

const animatedKg = ref(0);
const animatedEmpresas = ref(0);
const animatedIntercambios = ref(0);
const totalKg = computed(() => props.stats?.kgRecuperados || 0);
const totalKgDisplay = computed(() => animatedKg.value.toLocaleString('es'));

onMounted(() => {
    const duration = 2500;
    const start = performance.now();
    const targets = {
        kg: totalKg.value,
        empresas: props.stats?.empresas || 0,
        intercambios: props.stats?.intercambios || 0,
    };
    const step = (now: number) => {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4);
        animatedKg.value = Math.round(targets.kg * eased);
        animatedEmpresas.value = Math.round(targets.empresas * eased);
        animatedIntercambios.value = Math.round(targets.intercambios * eased);
        if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
});

const features = [
    { icon: MapPin, title: 'Mapa Interactivo', desc: 'Encuentra empresas cercanas y calcula distancias exactas.' },
    { icon: MessageSquare, title: 'Chat en Tiempo Real', desc: 'Comunícate, envía archivos y negocia intercambios directamente.' },
    { icon: ShieldCheck, title: 'Seguridad Avanzada', desc: 'Validación de archivos, bloqueo de empresas y control de roles.' },
    { icon: TrendingUp, title: 'Reportes y Estadísticas', desc: 'Mide tu impacto ambiental con gráficos detallados en tiempo real.' },
    { icon: Globe2, title: 'Red de Empresas', desc: 'Conecta con empresas de toda la región y expande tus oportunidades.' },
    { icon: Users, title: 'Gestión de Equipo', desc: 'Administra empleados con roles y control de acceso.' },
];

const steps = [
    { icon: Building2, title: 'Registra tu Empresa', desc: 'Crea la cuenta, completa tu perfil y configura categorías en minutos.' },
    { icon: PackageCheck, title: 'Publica Materiales', desc: 'Sube materiales de desecho. El jefe aprueba y quedan visibles.' },
    { icon: Handshake, title: 'Intercambia', desc: 'Explora el mapa, encuentra lo que necesitas y haz el intercambio.' },
];
</script>

<template>

    <Head title="EcoMatch - Economía Circular" />

    <div class="min-h-screen bg-background text-foreground overflow-x-hidden relative">

        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-primary/8 rounded-full blur-[150px]"
                style="animation: drift1 20s ease-in-out infinite;"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[700px] h-[700px] bg-emerald-500/5 rounded-full blur-[150px]"
                style="animation: drift2 25s ease-in-out infinite;"></div>
            <div class="absolute top-[30%] right-[20%] w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-[120px]"
                style="animation: drift3 18s ease-in-out infinite;"></div>
        </div>

        <header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl bg-background/50 border-b border-border/20">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-emerald-600 shadow-lg shadow-primary/30">
                        <Leaf class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-lg font-bold tracking-tight">EcoMatch</span>
                </div>
                <nav class="flex items-center gap-2">
                    <Link href="/login">
                        <Button variant="ghost" class="text-muted-foreground hover:text-foreground text-sm">Iniciar
                            Sesión</Button>
                    </Link>
                    <Link href="/register">
                        <Button
                            class="bg-primary hover:bg-primary/90 text-primary-foreground text-sm shadow-lg shadow-primary/30">
                            Registrar Empresa
                            <ArrowRight class="ml-1.5 h-3.5 w-3.5" />
                        </Button>
                    </Link>
                </nav>
            </div>
        </header>

        <section class="relative z-10 min-h-screen flex items-center justify-center px-6">
            <div class="flex flex-col items-center text-center max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-sm text-primary mb-8 backdrop-blur-md">
                    <Sparkles class="h-3.5 w-3.5" />
                    Plataforma de Economía Circular
                </div>

                <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight leading-[1.05] mb-6">
                    Conectando empresas<br>para un<br>
                    <span class="text-primary">futuro sostenible</span>
                </h1>

                <p class="max-w-2xl text-base sm:text-lg md:text-xl text-muted-foreground leading-8 mb-10">
                    Reduce tus residuos industriales y maximiza el valor de tus materiales. Únete a la red de empresas
                    que están transformando la economía circular.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <Link href="/register">
                        <Button size="lg"
                            class="h-14 px-8 text-base bg-primary hover:bg-primary/90 text-primary-foreground shadow-2xl shadow-primary/30">
                            Comenzar Gratis
                            <ArrowRight class="ml-2 h-5 w-5" />
                        </Button>
                    </Link>
                    <Link href="/login">
                        <Button size="lg" variant="outline"
                            class="h-14 px-8 text-base border-border/60 bg-background/40 backdrop-blur-md">Ya tengo
                            cuenta</Button>
                    </Link>
                </div>

                <div class="flex items-center gap-6 sm:gap-8 mt-12">
                    <div class="text-center">
                        <p class="text-2xl sm:text-3xl font-bold text-primary tabular-nums">{{ animatedEmpresas }}+</p>
                        <p class="text-xs text-muted-foreground">Empresas</p>
                    </div>
                    <div class="w-px h-8 bg-border/30"></div>
                    <div class="text-center">
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-500 tabular-nums">{{ animatedIntercambios
                            }}+</p>
                        <p class="text-xs text-muted-foreground">Intercambios</p>
                    </div>
                    <div class="w-px h-8 bg-border/30"></div>
                    <div class="text-center">
                        <p class="text-2xl sm:text-3xl font-bold text-blue-500 tabular-nums">{{ totalKgDisplay }}</p>
                        <p class="text-xs text-muted-foreground">Kg recuperados</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative z-10 py-24 overflow-hidden">
            <div class="impact-section">
                <div v-for="n in 30" :key="n" class="impact-particle" :style="{
                    top: `${Math.random() * 100}%`,
                    left: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 5}s`,
                    animationDuration: `${4 + Math.random() * 6}s`,
                    width: `${2 + Math.random() * 4}px`,
                    height: `${2 + Math.random() * 4}px`,
                }"></div>

                <div class="impact-rings">
                    <div class="impact-ring impact-ring-1"></div>
                    <div class="impact-ring impact-ring-2"></div>
                    <div class="impact-ring impact-ring-3"></div>
                </div>

                <div class="relative z-20 text-center">
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <Leaf class="h-5 w-5 text-emerald-400" />
                        <p class="text-sm text-emerald-400 font-semibold uppercase tracking-widest">Impacto Ambiental
                        </p>
                        <Leaf class="h-5 w-5 text-emerald-400" />
                    </div>

                    <p class="impact-number">{{ totalKgDisplay }}</p>

                    <p class="impact-label">kg recuperados</p>

                    <div class="impact-bar"></div>

                    <p class="text-base sm:text-lg text-muted-foreground max-w-md mx-auto mt-6">
                        Has evitado que <strong class="text-emerald-400">{{ totalKgDisplay }} kg</strong> de materiales
                        fueran a la basura.
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 max-w-6xl mx-auto px-6 py-20">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2">Todo lo que necesitas</h2>
                <p class="text-muted-foreground text-sm sm:text-base">Herramientas para impulsar la economía circular
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="feature in features" :key="feature.title"
                    class="group relative overflow-hidden rounded-2xl border border-border/50 bg-card/40 backdrop-blur-md p-5 hover:border-primary/30 hover:bg-card/70 hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 border border-primary/15 shrink-0 group-hover:bg-primary/15 transition-colors">
                            <component :is="feature.icon" class="h-5 w-5 text-primary" />
                        </div>
                        <h3 class="font-semibold text-sm sm:text-base">{{ feature.title }}</h3>
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground leading-6">{{ feature.desc }}</p>
                </div>
            </div>
        </section>

        <section class="relative z-10 max-w-5xl mx-auto px-6 py-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2">¿Cómo funciona <span
                        class="text-primary">EcoMatch</span>?</h2>
                <p class="text-muted-foreground text-sm sm:text-base">En 3 simples pasos</p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    class="hidden md:block absolute top-8 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-primary/0 via-primary/30 to-primary/0">
                </div>
                <div v-for="(step, index) in steps" :key="index"
                    class="relative flex flex-col items-center text-center">
                    <div class="relative mb-5">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary/15 to-primary/5 border border-primary/20 shadow-lg shadow-primary/10">
                            <component :is="step.icon" class="h-7 w-7 text-primary" />
                        </div>
                        <span
                            class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-white text-xs font-bold shadow-lg">{{
                            index + 1 }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold mb-2">{{ step.title }}</h3>
                    <p class="text-xs sm:text-sm text-muted-foreground leading-6 max-w-xs">{{ step.desc }}</p>
                </div>
            </div>
        </section>

        <section class="relative z-10 max-w-4xl mx-auto px-6 py-20">
            <div
                class="relative overflow-hidden rounded-3xl border border-primary/20 bg-gradient-to-br from-primary/10 via-primary/5 to-emerald-500/5 backdrop-blur-md p-8 sm:p-12 text-center">
                <div class="absolute -right-20 -top-20 w-60 h-60 bg-primary/10 rounded-full blur-[80px]"></div>
                <div class="absolute -left-20 -bottom-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-[80px]"></div>
                <div class="relative">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/15 border border-primary/20 mx-auto mb-4">
                        <Leaf class="h-7 w-7 text-primary" />
                    </div>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3">¿Listo para empezar?</h2>
                    <p class="text-sm sm:text-base text-muted-foreground mb-6 max-w-lg mx-auto">Únete hoy y transforma
                        los residuos de tu empresa en recursos valiosos.</p>
                    <Link href="/register">
                        <Button size="lg"
                            class="h-14 px-8 text-base bg-primary hover:bg-primary/90 text-primary-foreground shadow-2xl shadow-primary/30">
                            Registrar mi Empresa Gratis
                            <ArrowRight class="ml-2 h-5 w-5" />
                        </Button>
                    </Link>
                </div>
            </div>
        </section>

        <footer class="relative z-10 border-t border-border/20 mt-8">
            <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                        <Leaf class="h-4 w-4 text-primary" />
                    </div>
                    <span class="font-bold">EcoMatch</span>
                    <span class="text-xs text-muted-foreground ml-1">Economía Circular</span>
                </div>
                <p class="text-xs text-muted-foreground">© 2026 EcoMatch. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes drift1 {

    0%,
    100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(50px, 30px);
    }
}

@keyframes drift2 {

    0%,
    100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(-40px, -20px);
    }
}

@keyframes drift3 {

    0%,
    100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(30px, -40px);
    }
}

.impact-section {
    position: relative;
    height: 480px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: visible;
}

.impact-particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.5);
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    animation: float-particle 6s ease-in-out infinite;
}

@keyframes float-particle {

    0%,
    100% {
        transform: translateY(0) translateX(0);
        opacity: 0.2;
    }

    50% {
        transform: translateY(-50px) translateX(25px);
        opacity: 0.8;
    }
}

.impact-rings {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.impact-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    border: 1px solid rgba(16, 185, 129, 0.15);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: ring-pulse 4s ease-in-out infinite;
}

.impact-ring-1 {
    width: 200px;
    height: 200px;
    animation-delay: 0s;
}

.impact-ring-2 {
    width: 350px;
    height: 350px;
    animation-delay: 0.8s;
    border-color: rgba(16, 185, 129, 0.1);
}

.impact-ring-3 {
    width: 500px;
    height: 500px;
    animation-delay: 1.6s;
    border-color: rgba(16, 185, 129, 0.05);
}

@keyframes ring-pulse {

    0%,
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.5;
    }

    50% {
        transform: translate(-50%, -50%) scale(1.15);
        opacity: 1;
    }
}

.impact-number {
    font-size: clamp(4rem, 15vw, 9rem);
    font-weight: 900;
    line-height: 1;
    color: #10b981;
    text-shadow:
        0 0 20px rgba(16, 185, 129, 0.5),
        0 0 40px rgba(16, 185, 129, 0.3),
        0 0 80px rgba(16, 185, 129, 0.2);
    animation: number-glow 3s ease-in-out infinite;
    letter-spacing: -0.02em;
}

@keyframes number-glow {

    0%,
    100% {
        text-shadow:
            0 0 20px rgba(16, 185, 129, 0.5),
            0 0 40px rgba(16, 185, 129, 0.3),
            0 0 80px rgba(16, 185, 129, 0.2);
    }

    50% {
        text-shadow:
            0 0 30px rgba(16, 185, 129, 0.8),
            0 0 60px rgba(16, 185, 129, 0.5),
            0 0 120px rgba(16, 185, 129, 0.3);
    }
}

.impact-label {
    font-size: clamp(1.25rem, 4vw, 2rem);
    font-weight: 700;
    color: #34d399;
    margin-top: 0.5rem;
    letter-spacing: 0.05em;
}

.impact-bar {
    width: 120px;
    height: 2px;
    margin: 1.5rem auto 0;
    background: linear-gradient(to right, transparent, rgba(16, 185, 129, 0.6), transparent);
}

@media (max-width: 640px) {
    .impact-section {
        height: 360px;
    }

    .impact-ring-3 {
        display: none;
    }

    .impact-ring-2 {
        width: 280px;
        height: 280px;
    }
}
</style>