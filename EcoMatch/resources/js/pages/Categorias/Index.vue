<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Categoria {
    idcategorias: number;
    nombre: string;
    descripcion: string | null;
}

const props = defineProps<{
    categorias: Categoria[];
}>();

const form = useForm({
    nombre: '',
    descripcion: '',
    empresa_idempresa: 1, 
});

function submit() {
    form.post('/categorias', {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <div class="p-6 bg-background text-foreground min-h-screen">
        <Head title="Categorías" />

        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl font-semibold mb-6">Gestión de Categorías</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Card class="h-fit">
                    <CardHeader>
                        <CardTitle>Crear Nueva Categoría</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <Label for="nombre">Nombre</Label>
                                <Input id="nombre" v-model="form.nombre" placeholder="Ej: Plásticos" />
                                <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">{{ form.errors.nombre }}</p>
                            </div>
                            <div>
                                <Label for="descripcion">Descripción</Label>
                                <Input id="descripcion" v-model="form.descripcion" placeholder="Opcional" />
                            </div>
                            <Button type="submit" :disabled="form.processing">
                                Guardar Categoría
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Categorías Registradas</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead>Descripción</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="cat in props.categorias" :key="cat.idcategorias">
                                    <TableCell>{{ cat.idcategorias }}</TableCell>
                                    <TableCell class="font-medium">{{ cat.nombre }}</TableCell>
                                    <TableCell>{{ cat.descripcion || 'N/A' }}</TableCell>
                                </TableRow>
                                <TableRow v-if="props.categorias.length === 0">
                                    <TableCell colspan="3" class="text-center text-muted-foreground py-4">
                                        No hay categorías registradas.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>