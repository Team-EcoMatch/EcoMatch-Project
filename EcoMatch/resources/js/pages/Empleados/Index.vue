<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { 
    AlertDialog, 
    AlertDialogAction, 
    AlertDialogCancel, 
    AlertDialogContent, 
    AlertDialogDescription, 
    AlertDialogFooter, 
    AlertDialogHeader, 
    AlertDialogTitle, 
    AlertDialogTrigger 
} from '@/components/ui/alert-dialog';
import { Trash2, CheckCircle2, X } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

interface Empleado {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    empleados: Empleado[];
}>();

const page = usePage();
const showNotification = ref(false);
const notificationMessage = ref('');

onMounted(() => {
    if (page.props.message) {
        notificationMessage.value = page.props.message as string;
        showNotification.value = true;
        setTimeout(() => { showNotification.value = false; }, 3000);
    }
});

const form = useForm({
    name: '',
    email: '',
    password: '',
});

function submit() {
    form.post('/empleados', {
        onSuccess: () => {
            form.reset();
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
            <div v-if="showNotification" class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden p-4">
                <div class="flex items-start gap-3">
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
            </div>
        </Transition>

        <div class="max-w-7xl mx-auto space-y-6">
            <div>
                <h2 class="text-2xl font-semibold">Gestión de Empleados</h2>
                <p class="text-muted-foreground text-sm mt-1">Crea cuentas para los empleados de tu empresa.</p>
            </div>

            <Card class="bg-card border-border shadow-none">
                <CardHeader>
                    <CardTitle>Agregar Nuevo Empleado</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="grid md:grid-cols-3 gap-4 items-end">
                        <div>
                            <Label for="name">Nombre completo</Label>
                            <Input id="name" v-model="form.name" required class="bg-background border-border" placeholder="Juan Pérez" />
                        </div>
                        <div>
                            <Label for="email">Correo electrónico</Label>
                            <Input id="email" type="email" v-model="form.email" required class="bg-background border-border" placeholder="empleado@empresa.com" />
                        </div>
                        <div>
                            <Label for="password">Contraseña temporal</Label>
                            <Input id="password" type="password" v-model="form.password" required class="bg-background border-border" placeholder="Mínimo 8 caracteres" />
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <Button type="submit" :disabled="form.processing">Crear Empleado</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card class="bg-card border-border shadow-none">
                <CardHeader>
                    <CardTitle>Empleados Actuales</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-border">
                                <tr class="text-left text-muted-foreground">
                                    <th class="pb-2">Nombre</th>
                                    <th class="pb-2">Correo</th>
                                    <th class="pb-2 text-right">Rol</th>
                                    <th class="pb-2 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="props.empleados.length === 0">
                                    <td colspan="4" class="py-6 text-center text-muted-foreground">No tienes empleados registrados todavía.</td>
                                </tr>
                                <tr v-for="emp in props.empleados" :key="emp.id" class="border-b border-border/50">
                                    <td class="py-3 font-medium">{{ emp.name }}</td>
                                    <td class="py-3 text-muted-foreground">{{ emp.email }}</td>
                                    <td class="py-3 text-right">
                                        <Badge variant="outline" class="border-primary text-primary">Empleado</Badge>
                                    </td>
                                    <td class="py-3 text-right">
                                        <AlertDialog>
                                            <AlertDialogTrigger as-child>
                                                <Button variant="destructive" size="sm" class="bg-red-600 hover:bg-red-700 text-white">
                                                    <Trash2 class="w-4 h-4 mr-1" />
                                                    Eliminar
                                                </Button>
                                            </AlertDialogTrigger>
                                            <AlertDialogContent class="bg-card border-border text-foreground">
                                                <AlertDialogHeader>
                                                    <AlertDialogTitle>¿Estás completamente seguro?</AlertDialogTitle>
                                                    <AlertDialogDescription class="text-muted-foreground">
                                                        Esta acción no se puede deshacer. Se eliminará permanentemente la cuenta del empleado "{{ emp.name }}".
                                                    </AlertDialogDescription>
                                                </AlertDialogHeader>
                                                <AlertDialogFooter>
                                                    <AlertDialogCancel class="border-border text-muted-foreground hover:bg-accent hover:text-foreground">
                                                        Cancelar
                                                    </AlertDialogCancel>
                                                    <AlertDialogAction @click="eliminarEmpleado(emp.id)" class="bg-red-600 hover:bg-red-700 text-white">
                                                        Sí, eliminar
                                                    </AlertDialogAction>
                                                </AlertDialogFooter>
                                            </AlertDialogContent>
                                        </AlertDialog>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>