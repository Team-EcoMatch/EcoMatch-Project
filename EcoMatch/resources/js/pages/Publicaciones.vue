<script setup>
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Trash2, CheckCircle } from '@lucide/vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

const props = defineProps({
    publicaciones: Object,
    filters: Object,
    estadosValidos: Array,
})

const search = ref(props.filters?.search || '')
const estado = ref(props.filters?.estado || '')

// Filtro con debounce automático
let timeout = null
watch([search, estado], () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        router.get(
            '/admin/publicaciones',
            { search: search.value, estado: estado.value },
            { preserveState: true, replace: true }
        )
    }, 300)
})

const cambiarEstado = (id, nuevoEstado) => {
    router.patch(`/admin/publicaciones/${id}/estado`, { estado: nuevoEstado }, {
        preserveScroll: true
    })
}

const eliminarPublicacion = (id, titulo) => {
    if (confirm(`¿Estás seguro de eliminar la publicación "${titulo}"?`)) {
        router.delete(`/admin/publicaciones/${id}`, {
            preserveScroll: true
        })
    }
}

const aprobarPublicacion = (id) => {
    if (confirm('¿Aprobar esta publicación? Pasará a estar disponible.')) {
        router.patch(`/admin/publicaciones/${id}/approve`, {}, {
            preserveScroll: true
        })
    }
}

const rechazarPublicacion = (id) => {
    if (confirm('¿Rechazar esta publicación? Pasará a estado "Rechazado".')) {
        router.patch(`/admin/publicaciones/${id}/reject`, {}, {
            preserveScroll: true
        })
    }
}

// Función para obtener las clases del badge según el estado
const getBadgeClasses = (estado) => {
    const colors = {
        'Pendiente': 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-400 dark:border-yellow-900/50',
        'Disponible': 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50',
        'Inactivo': 'bg-muted text-muted-foreground border-border',
        'Agotado': 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/40 dark:text-orange-400 dark:border-orange-900/50',
        'Reservado': 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50',
        'Intercambiado': 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-900/50',
        'Rechazado': 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50',
    }
    return colors[estado] || 'bg-muted text-muted-foreground border-border'
}
</script>

<template>

    <Head title="Admin - Publicaciones" />

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Moderación de Publicaciones</h1>
                <p class="text-sm text-muted-foreground">Supervisa, activa, desactiva o elimina materiales del catálogo
                    general.
                </p>
            </div>
        </div>

        <!-- Filtros -->
        <div
            class="bg-card text-card-foreground p-4 rounded-xl border border-border mb-6 flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input v-model="search" type="text" placeholder="Buscar por material o empresa..."
                    class="w-full px-4 py-2 border border-input rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-background text-foreground" />
            </div>
            <div class="w-full md:w-56">
                <select v-model="estado"
                    class="w-full px-4 py-2 border border-input rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-background text-foreground">
                    <option value="">Todos los estados</option>
                    <option v-for="est in estadosValidos" :key="est" :value="est">{{ est }}</option>
                </select>
            </div>
        </div>

        <!-- Grid de tarjetas -->
        <div v-if="publicaciones.data.length === 0"
            class="text-center py-12 text-muted-foreground border border-dashed rounded-lg">
            <p class="text-lg">No hay publicaciones disponibles con los filtros aplicados.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Card v-for="pub in publicaciones.data" :key="pub.idpublicaciones"
                class="hover:shadow-md transition-shadow overflow-hidden">
                <CardHeader class="p-5 pb-3">
                    <div class="flex justify-between items-center gap-2">
                        <CardTitle class="text-base font-semibold text-foreground line-clamp-1">{{ pub.nombre }}
                        </CardTitle>
                        <Badge variant="outline" :class="getBadgeClasses(pub.estado)">
                            {{ pub.estado }}
                        </Badge>
                    </div>
                </CardHeader>

                <CardContent class="p-5 pt-0">
                    <!-- Imagen -->
                    <div class="aspect-video w-full overflow-hidden rounded-md bg-muted mb-4">
                        <img :src="pub.urlImagen" :alt="pub.nombre" class="w-full h-full object-cover"
                            @error="(e) => e.target.src = '/images/placeholder.png'" />
                    </div>

                    <!-- Descripción -->
                    <p class="text-sm text-muted-foreground mb-3 line-clamp-2">{{ pub.descripcion }}</p>

                    <!-- Cantidad -->
                    <div class="flex justify-between text-sm font-medium mb-1">
                        <span class="text-muted-foreground">Cantidad:</span>
                        <span class="text-foreground">{{ pub.cantidad }} {{ pub.unidadMedida }}</span>
                    </div>

                    <!-- Metadatos -->
                    <div class="text-xs text-muted-foreground mt-3 pt-3 border-t border-border space-y-0.5">
                        <div>Publicado por: <strong class="text-foreground">{{ pub.empresa?.nombreEmpresa || 'N/A'
                                }}</strong></div>
                        <div>Categoría: <strong class="text-foreground">{{ pub.categoria?.nombre || 'N/A' }}</strong>
                        </div>
                        <div>Frecuencia: <strong class="text-foreground">{{ pub.frecuencia }}</strong></div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-border pt-4">
                        <!-- Select para cambiar estado -->
                        <div class="flex-1">
                            <select :value="pub.estado"
                                @change="cambiarEstado(pub.idpublicaciones, $event.target.value)"
                                class="w-full text-xs border-input rounded-md py-1.5 pl-2 pr-7 focus:ring-emerald-500 focus:border-emerald-500 bg-background text-foreground">
                                <option v-for="est in estadosValidos" :key="est" :value="est">{{ est }}</option>
                            </select>
                        </div>

                        <!-- Botón Aprobar (solo si está Pendiente) -->
                        <button v-if="pub.estado === 'Pendiente'" @click="aprobarPublicacion(pub.idpublicaciones)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md transition-colors shadow-sm">
                            <CheckCircle class="w-3.5 h-3.5" />
                            Aprobar
                        </button>

                        <!-- Botón Rechazar (solo si está Pendiente) -->
                        <button v-if="pub.estado === 'Pendiente'" @click="rechazarPublicacion(pub.idpublicaciones)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-md transition-colors shadow-sm">
                            <XCircle class="w-3.5 h-3.5" /> <!-- O usa un icono que tengas disponible -->
                            Rechazar
                        </button>
                        <!-- Botón Eliminar -->
                        <button @click="eliminarPublicacion(pub.idpublicaciones, pub.nombre)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-100 dark:bg-rose-900/30 hover:bg-rose-200 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 text-xs font-medium rounded-md transition-colors">
                            <Trash2 class="w-3.5 h-3.5" />
                            Eliminar
                        </button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Paginación -->
        <div class="mt-6 flex justify-between items-center text-sm text-muted-foreground"
            v-if="publicaciones.total > 0">
            <div>Mostrando {{ publicaciones.from }} a {{ publicaciones.to }} de {{ publicaciones.total }} registros
            </div>
            <div class="flex gap-2">
                <button v-if="publicaciones.prev_page_url" @click="router.get(publicaciones.prev_page_url)"
                    class="px-3 py-1 border border-input rounded bg-card text-card-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                    Anterior
                </button>
                <button v-if="publicaciones.next_page_url" @click="router.get(publicaciones.next_page_url)"
                    class="px-3 py-1 border border-input rounded bg-card text-card-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</template>
