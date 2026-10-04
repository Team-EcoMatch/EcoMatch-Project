<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader,
    AlertDialogTitle, AlertDialogTrigger
} from '@/components/ui/alert-dialog';
import {
    Pencil, Trash2, CheckCircle2, X, Plus, Search, Tags,
    Recycle, PackageCheck, Building2, ShieldCheck, Leaf
} from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';

interface Categoria {
    idcategorias: number;
    nombre: string;
    descripcion: string | null;
    publicaciones_count: number;
}

const props = defineProps<{
    categorias: Categoria[];
    message: string | null;
}>();

const showNotification = ref(false);
const notificationMessage = ref(props.message || '');
const searchQuery = ref('');

onMounted(() => {
    if (props.message) {
        triggerNotification(props.message);
    }
});

function triggerNotification(msg: string) {
    notificationMessage.value = msg;
    showNotification.value = true;
    setTimeout(() => { showNotification.value = false; }, 3000);
}

const filteredCategorias = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.categorias;
    return props.categorias.filter(cat =>
        cat.nombre.toLowerCase().includes(query) ||
        (cat.descripcion || '').toLowerCase().includes(query)
    );
});

const categoryConfig: Record<string, { icon: any; color: string; bg: string; border: string; top: string }> = {
    'plásticos': { icon: Recycle, color: 'text-emerald-500', bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', top: 'bg-emerald-500' },
    'plasticos': { icon: Recycle, color: 'text-emerald-500', bg: 'bg-emerald-500/10', border: 'border-emerald-500/20', top: 'bg-emerald-500' },
    'cartón': { icon: PackageCheck, color: 'text-amber-500', bg: 'bg-amber-500/10', border: 'border-amber-500/20', top: 'bg-amber-500' },
    'carton': { icon: PackageCheck, color: 'text-amber-500', bg: 'bg-amber-500/10', border: 'border-amber-500/20', top: 'bg-amber-500' },
    'metales': { icon: Building2, color: 'text-blue-500', bg: 'bg-blue-500/10', border: 'border-blue-500/20', top: 'bg-blue-500' },
    'metal': { icon: Building2, color: 'text-blue-500', bg: 'bg-blue-500/10', border: 'border-blue-500/20', top: 'bg-blue-500' },
    'vidrio': { icon: ShieldCheck, color: 'text-purple-500', bg: 'bg-purple-500/10', border: 'border-purple-500/20', top: 'bg-purple-500' },
    'textiles': { icon: Tags, color: 'text-pink-500', bg: 'bg-pink-500/10', border: 'border-pink-500/20', top: 'bg-pink-500' },
    'textil': { icon: Tags, color: 'text-pink-500', bg: 'bg-pink-500/10', border: 'border-pink-500/20', top: 'bg-pink-500' },
    'orgánicos': { icon: Leaf, color: 'text-lime-500', bg: 'bg-lime-500/10', border: 'border-lime-500/20', top: 'bg-lime-500' },
    'organicos': { icon: Leaf, color: 'text-lime-500', bg: 'bg-lime-500/10', border: 'border-lime-500/20', top: 'bg-lime-500' },
    'madera': { icon: Leaf, color: 'text-orange-500', bg: 'bg-orange-500/10', border: 'border-orange-500/20', top: 'bg-orange-500' },
};

const defaultConfig = { icon: Tags, color: 'text-primary', bg: 'bg-primary/10', border: 'border-primary/20', top: 'bg-primary' };

function getConfig(nombre: string) {
    const key = nombre.toLowerCase().trim();
    return categoryConfig[key] || defaultConfig;
}

function deleteCategory(id: number) {
    useForm({}).delete('/categorias/' + id, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            const msg = (page.props.message as string) || 'Categoría eliminada correctamente.';
            triggerNotification(msg);
        }
    });
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Categorías" />

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
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold">Gestión de Categorías</h2>
                    <p class="text-sm text-muted-foreground mt-1">Organiza tus materiales por tipo</p>
                </div>
                <Link href="/categorias/create">
                    <Button class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg">
                        <Plus class="w-4 h-4 mr-2" />
                        Crear Categoría
                    </Button>
                </Link>
            </div>

            <div v-if="props.categorias.length > 0" class="relative mb-6">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Buscar categoría por nombre o descripción..." class="pl-10" />
            </div>

            <div v-if="filteredCategorias.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Card v-for="cat in filteredCategorias" :key="cat.idcategorias"
                    class="relative overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div :class="['absolute', 'top-0', 'left-0', 'w-full', 'h-1', getConfig(cat.nombre).top]"></div>
                    <CardContent class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                :class="['flex', 'h-12', 'w-12', 'items-center', 'justify-center', 'rounded-xl', getConfig(cat.nombre).bg]">
                                <component :is="getConfig(cat.nombre).icon"
                                    :class="['h-6', 'w-6', getConfig(cat.nombre).color]" />
                            </div>
                            <Badge v-if="cat.publicaciones_count > 0" variant="outline"
                                :class="[getConfig(cat.nombre).bg, getConfig(cat.nombre).color, 'border-0']">
                                {{ cat.publicaciones_count }} publicación{{ cat.publicaciones_count !== 1 ? 'es' : '' }}
                            </Badge>
                            <Badge v-else variant="outline" class="bg-muted text-muted-foreground border-0">
                                Sin publicaciones
                            </Badge>
                        </div>

                        <h3 class="text-lg font-bold mb-1">{{ cat.nombre }}</h3>
                        <p class="text-sm text-muted-foreground mb-4 line-clamp-2">
                            {{ cat.descripcion || 'Sin descripción' }}
                        </p>

                        <div class="flex justify-end gap-2 border-t border-border pt-4">
                            <Link :href="`/categorias/${cat.idcategorias}/edit`">
                                <Button size="sm" variant="outline"
                                    class="border-primary text-primary hover:bg-accent hover:text-primary">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Editar
                                </Button>
                            </Link>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button size="sm" variant="destructive">
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Eliminar
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent class="bg-card border-border text-foreground">
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                        <AlertDialogDescription class="text-muted-foreground">
                                            Esta acción no se puede deshacer. Se eliminará permanentemente la categoría
                                            "{{ cat.nombre }}".
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                            Cancelar
                                        </AlertDialogCancel>
                                        <AlertDialogAction @click="deleteCategory(cat.idcategorias)"
                                            class="bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                                            Sí, eliminar
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else-if="props.categorias.length > 0 && searchQuery" class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <Search class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p class="text-lg">No se encontraron categorías</p>
                    <p class="text-sm mt-2">Prueba con otro término de búsqueda</p>
                </CardContent>
            </Card>

            <Card v-else class="border-dashed">
                <CardContent class="text-center text-muted-foreground py-12">
                    <Tags class="w-12 h-12 mx-auto mb-3 opacity-30" />
                    <p class="text-lg">No hay categorías registradas</p>
                    <p class="text-sm mt-2">¡Crea la primera categoría!</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<style>
@keyframes toast-progress {
    from {
        width: 100%;
    }

    to {
        width: 0%;
    }
}
</style>