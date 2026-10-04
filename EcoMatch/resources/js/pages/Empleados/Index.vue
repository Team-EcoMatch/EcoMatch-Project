<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import {
    AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent,
    AlertDialogDescription, AlertDialogFooter, AlertDialogHeader,
    AlertDialogTitle, AlertDialogTrigger
} from '@/components/ui/alert-dialog';
import {
    Trash2, CheckCircle2, X, Search, Users, UserPlus, ShieldCheck, Check
} from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';

interface Empleado {
    id: number;
    name: string;
    email: string;
    estado: number | boolean;
    created_at: string;
    email_verified_at: string | null;
}

const props = defineProps<{
    empleados: Empleado[];
}>();

const page = usePage();
const showNotification = ref(false);
const notificationMessage = ref('');
const searchQuery = ref('');

onMounted(() => {
    if (page.props.message) {
        notificationMessage.value = page.props.message as string;
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 3000);
    }
});

const filteredEmpleados = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.empleados;
    return props.empleados.filter(emp =>
        emp.name.toLowerCase().includes(query) || emp.email.toLowerCase().includes(query)
    );
});

const stats = computed(() => ({
    total: props.empleados.length,
    activos: props.empleados.filter(e => Number(e.estado) === 1).length,
    inactivos: props.empleados.filter(e => Number(e.estado) !== 1).length,
}));

const requisitos = computed(() => [
    { label: 'Mínimo 8 caracteres', cumplido: form.password.length >= 8 },
    { label: 'Una letra mayúscula (A-Z)', cumplido: /[A-Z]/.test(form.password) },
    { label: 'Una letra minúscula (a-z)', cumplido: /[a-z]/.test(form.password) },
    { label: 'Un número (0-9)', cumplido: /[0-9]/.test(form.password) },
    { label: 'Un carácter especial (!@#$%^&*)', cumplido: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(form.password) },
]);

const todosCumplidos = computed(() => requisitos.value.every(r => r.cumplido));

function getAvatarColor(nombre: string): { gradient: string; text: string } {
    const colors = [
        { gradient: 'from-blue-500/20 to-blue-500/5 border-blue-500/20', text: 'text-blue-500' },
        { gradient: 'from-emerald-500/20 to-emerald-500/5 border-emerald-500/20', text: 'text-emerald-500' },
        { gradient: 'from-amber-500/20 to-amber-500/5 border-amber-500/20', text: 'text-amber-500' },
        { gradient: 'from-purple-500/20 to-purple-500/5 border-purple-500/20', text: 'text-purple-500' },
        { gradient: 'from-pink-500/20 to-pink-500/5 border-pink-500/20', text: 'text-pink-500' },
    ];
    const hash = nombre.charCodeAt(0) % colors.length;
    return colors[hash];
}

function timeAgo(date: string): string {
    if (!date) return '';
    const now = new Date();
    const past = new Date(date);
    const diff = now.getTime() - past.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    if (days > 30) return past.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
    if (days > 0) return `Hace ${days}d`;
    return 'Hoy';
}

const form = useForm({
    name: '',
    email: '',
    password: '',
});

function submit() {
    if (!todosCumplidos.value) return;
    form.post('/empleados', {
        onSuccess: () => {
            form.reset();
        }
    });
}

function toggleEstado(id: number) {
    router.patch(`/empleados/${id}/estado`, {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            const msg = (page.props.message as string) || 'Estado actualizado.';
            notificationMessage.value = msg;
            showNotification.value = true;
            setTimeout(() => { showNotification.value = false; }, 3000);
        }
    });
}

function eliminarEmpleado(id: number) {
    useForm({}).delete('/empleados/' + id, {
        preserveScroll: true,
        onSuccess: (page) => {
            const msg = (page.props.message as string) || 'Empleado eliminado correctamente.';
            notificationMessage.value = msg;
            showNotification.value = true;
            setTimeout(() => { showNotification.value = false; }, 3000);
        }
    });
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Gestión de Empleados" />

        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showNotification"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden p-4">
                <div class="flex items-start gap-3">
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

        <div class="max-w-7xl mx-auto space-y-6">
            <div
                class="relative overflow-hidden rounded-2xl mb-2 border border-border bg-gradient-to-br from-primary/10 via-primary/5 to-transparent">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/10 rounded-full blur-[60px]"></div>
                <div class="relative p-6 flex items-center gap-5">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-primary/15 border border-primary/20">
                        <Users class="h-8 w-8 text-primary" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Gestión de Empleados</h2>
                        <p class="text-sm text-muted-foreground mt-1">Crea cuentas para los empleados de tu empresa</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <Card
                    class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/15 shrink-0">
                            <Users class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.total }}</p>
                            <p class="text-xs text-muted-foreground">Total</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                            <CheckCircle2 class="h-5 w-5 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.activos }}</p>
                            <p class="text-xs text-muted-foreground">Activos</p>
                        </div>
                    </CardContent>
                </Card>
                <Card
                    class="relative overflow-hidden border-gray-500/20 bg-gradient-to-br from-gray-500/10 to-transparent">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gray-500"></div>
                    <CardContent class="p-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-500/15 shrink-0">
                            <Users class="h-5 w-5 text-gray-500" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold tabular-nums">{{ stats.inactivos }}</p>
                            <p class="text-xs text-muted-foreground">Inactivos</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card class="border-border overflow-hidden">
                <CardHeader class="border-b border-border bg-muted/20">
                    <CardTitle class="flex items-center gap-2">
                        <UserPlus class="w-5 h-5 text-primary" />
                        Agregar Nuevo Empleado
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <Label for="name" class="text-muted-foreground mb-2 block">Nombre completo</Label>
                                <Input id="name" v-model="form.name" required class="bg-background border-border h-11"
                                    placeholder="Juan Pérez" />
                                <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}
                                </p>
                            </div>
                            <div>
                                <Label for="email" class="text-muted-foreground mb-2 block">Correo electrónico</Label>
                                <Input id="email" type="email" v-model="form.email" required
                                    class="bg-background border-border h-11" placeholder="empleado@empresa.com" />
                                <p v-if="form.errors.email" class="text-destructive text-xs mt-1">{{ form.errors.email
                                    }}</p>
                            </div>
                        </div>

                        <div>
                            <Label for="password" class="text-muted-foreground mb-2 block">Contraseña temporal</Label>
                            <Input id="password" type="password" v-model="form.password" required
                                class="bg-background border-border h-11" placeholder="Mínimo 8 caracteres" />
                            <p v-if="form.errors.password" class="text-destructive text-xs mt-1">{{ form.errors.password
                                }}</p>

                            <div v-if="form.password.length > 0"
                                class="mt-3 rounded-lg border border-border bg-muted/30 p-3 space-y-1.5">
                                <p class="text-xs font-semibold text-muted-foreground mb-2 flex items-center gap-1.5">
                                    <ShieldCheck class="w-3.5 h-3.5" />
                                    Requisitos de la contraseña:
                                </p>
                                <div v-for="req in requisitos" :key="req.label" class="flex items-center gap-2">
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

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing || !todosCumplidos"
                                class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg">
                                <UserPlus class="w-4 h-4 mr-2" />
                                {{ form.processing ? 'Creando...' : 'Crear Empleado' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card class="border-border overflow-hidden">
                <CardHeader class="border-b border-border bg-muted/20">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                        <CardTitle>Empleados Actuales</CardTitle>
                        <div v-if="props.empleados.length > 0" class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <input v-model="searchQuery" type="text" placeholder="Buscar empleado..."
                                class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-input bg-background text-sm text-foreground placeholder:text-muted-foreground outline-none focus-visible:ring-1 focus-visible:ring-primary" />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div v-if="filteredEmpleados.length > 0" class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-border">
                                <tr class="text-left text-muted-foreground">
                                    <th class="pb-3 px-4 pt-3 font-medium">Empleado</th>
                                    <th class="pb-3 px-4 font-medium">Estado</th>
                                    <th class="pb-3 px-4 font-medium">Registro</th>
                                    <th class="pb-3 px-4 text-right font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="emp in filteredEmpleados" :key="emp.id"
                                    class="border-b border-border/50 hover:bg-muted/30 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                :class="['flex', 'h-9', 'w-9', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(emp.name).gradient]">
                                                <span :class="['text-xs', 'font-bold', getAvatarColor(emp.name).text]">
                                                    {{ emp.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-foreground dark:text-white">{{ emp.name }}
                                                </div>
                                                <div class="text-xs text-muted-foreground">{{ emp.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                :class="['h-2', 'w-2', 'rounded-full', Number(emp.estado) === 1 ? 'bg-emerald-500' : 'bg-gray-400']"></span>
                                            <span class="text-xs text-muted-foreground">{{ Number(emp.estado) === 1 ?
                                                'Activo' : 'Inactivo' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-muted-foreground">{{ timeAgo(emp.created_at) }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button size="sm" variant="outline" @click="toggleEstado(emp.id)"
                                                :class="Number(emp.estado) === 1
                                                    ? 'border-amber-500/30 text-amber-600 hover:bg-amber-500/10'
                                                    : 'border-emerald-500/30 text-emerald-600 hover:bg-emerald-500/10'">
                                                {{ Number(emp.estado) === 1 ? 'Desactivar' : 'Activar' }}
                                            </Button>
                                            <AlertDialog>
                                                <AlertDialogTrigger as-child>
                                                    <Button variant="destructive" size="sm"
                                                        class="bg-red-600 hover:bg-red-700 text-white">
                                                        <Trash2 class="w-4 h-4 mr-1" />
                                                        Eliminar
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent class="bg-card border-border text-foreground">
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>¿Estás completamente seguro?
                                                        </AlertDialogTitle>
                                                        <AlertDialogDescription class="text-muted-foreground">
                                                            Esta acción no se puede deshacer. Se eliminará
                                                            permanentemente la cuenta del empleado "{{ emp.name }}".
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel
                                                            class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                                            Cancelar</AlertDialogCancel>
                                                        <AlertDialogAction @click="eliminarEmpleado(emp.id)"
                                                            class="bg-red-600 hover:bg-red-700 text-white">Sí, eliminar
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="filteredEmpleados.length > 0" class="md:hidden divide-y divide-border">
                        <div v-for="emp in filteredEmpleados" :key="emp.id"
                            class="p-4 hover:bg-muted/30 transition-colors">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="['flex', 'h-10', 'w-10', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(emp.name).gradient]">
                                        <span :class="['text-sm', 'font-bold', getAvatarColor(emp.name).text]">
                                            {{ emp.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-foreground dark:text-white truncate">{{ emp.name }}
                                        </div>
                                        <div class="text-xs text-muted-foreground truncate">{{ emp.email }}</div>
                                    </div>
                                </div>
                                <span
                                    :class="['h-2', 'w-2', 'rounded-full', 'shrink-0', Number(emp.estado) === 1 ? 'bg-emerald-500' : 'bg-gray-400']"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-muted-foreground">{{ timeAgo(emp.created_at) }}</span>
                                <div class="flex gap-2">
                                    <Button size="sm" variant="outline" @click="toggleEstado(emp.id)" :class="Number(emp.estado) === 1
                                        ? 'border-amber-500/30 text-amber-600 hover:bg-amber-500/10'
                                        : 'border-emerald-500/30 text-emerald-600 hover:bg-emerald-500/10'">
                                        {{ Number(emp.estado) === 1 ? 'Desactivar' : 'Activar' }}
                                    </Button>
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button variant="destructive" size="sm"
                                                class="bg-red-600 hover:bg-red-700 text-white">
                                                <Trash2 class="w-4 h-4" />
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent class="bg-card border-border text-foreground">
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                                <AlertDialogDescription class="text-muted-foreground">
                                                    Se eliminará permanentemente la cuenta del empleado "{{ emp.name
                                                    }}".
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel
                                                    class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                                    Cancelar</AlertDialogCancel>
                                                <AlertDialogAction @click="eliminarEmpleado(emp.id)"
                                                    class="bg-red-600 hover:bg-red-700 text-white">Sí, eliminar
                                                </AlertDialogAction>
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mx-auto mb-4">
                            <Users class="w-8 h-8 text-primary/50" />
                        </div>
                        <p class="text-lg font-medium text-muted-foreground">{{ searchQuery ? `No se encontraron
                            empleados` : `No tienes empleados registrados` }}</p>
                        <p class="text-sm text-muted-foreground mt-1">{{ searchQuery ? `Prueba con otro término` : `Crea
                            la primera cuenta arriba` }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>