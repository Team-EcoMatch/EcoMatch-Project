<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Publicacion {
    idpublicaciones: number;
    nombre: string;
    descripcion: string;
    cantidad: string | number;
    unidadMedida: string;
    frecuencia: string;
    estado: string;
    urlImagen: string;
    empresa: { nombreEmpresa: string } | null;
    categoria: { nombre: string } | null;
}

const props = defineProps<{
    publicaciones: Publicacion[];
}>();
</script>

<template>
        
        <div class="p-6 md:p-8">
            <div class="max-w-7xl mx-auto">
                
                <div v-if="props.publicaciones.length === 0" class="text-center py-12 text-muted-foreground border border-dashed rounded-lg">
                    <p class="text-lg">No hay publicaciones disponibles en este momento.</p>
                    <p class="text-sm mt-2">Intenta agregar datos a tu tabla `publicaciones` en MySQL.</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card v-for="pub in props.publicaciones" :key="pub.idpublicaciones">
                        <CardHeader>
                            <div class="flex justify-between items-center">
                                <CardTitle>{{ pub.nombre }}</CardTitle>
                                <Badge>{{ pub.estado }}</Badge>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <img :src="pub.urlImagen" alt="Imagen material" class="w-full h-40 object-cover rounded-md mb-4">
                            <p class="text-sm text-muted-foreground mb-2">{{ pub.descripcion }}</p>

                            <div class="flex justify-between text-sm font-semibold mb-2">
                                <span>Cantidad:</span>
                                <span>{{ pub.cantidad }} {{ pub.unidadMedida }}</span>
                            </div>

                            <div class="text-xs text-muted-foreground mt-4 border-t pt-2 border-border">
                                <div>Publicado por: <strong>{{ pub.empresa?.nombreEmpresa || 'N/A' }}</strong></div>
                                <div>Categoría: <strong>{{ pub.categoria?.nombre || 'N/A' }}</strong></div>
                                <div>Frecuencia: {{ pub.frecuencia }}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
</template>