<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { MessageSquare, ArrowRight, Building } from 'lucide-vue-next';

const props = defineProps<{
    chats: any[];
    empresaId: number;
}>();

function getOtraEmpresa(chat: any): string {
    const esOrigen = chat.idEmpresaOrigen === props.empresaId;
    const otra = esOrigen ? (chat.empresa_destino || chat.empresaDestino) : (chat.empresa_origen || chat.empresaOrigen);
    return otra?.nombreEmpresa || 'Empresa participante';
}
</script>

<template>

    <Head title="Mis Chats" />

    <div class="p-6 bg-background min-h-screen text-foreground">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <MessageSquare class="w-6 h-6 text-primary" />
                        Conversaciones de Intercambio
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Chats activos con las empresas con las que tienes solicitudes aceptadas.
                    </p>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-if="chats.length === 0"
                class="text-center py-16 border border-dashed rounded-xl text-muted-foreground bg-card">
                <MessageSquare class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p class="text-lg font-medium">No tienes chats activos.</p>
                <p class="text-sm mt-1">Los chats se activan automáticamente cuando una solicitud es aceptada.</p>
            </div>

            <!-- Lista de chats -->
            <Card v-else class="overflow-hidden">
                <div class="divide-y divide-border">
                    <Link v-for="chat in chats" :key="chat.idsolicitud" :href="`/chat/${chat.idsolicitud}`"
                        class="flex items-center gap-4 p-4 hover:bg-accent transition-colors group">
                        <!-- Avatar / ícono -->
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10">
                            <Building class="h-5 w-5 text-primary" />
                        </div>

                        <!-- Info principal -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h3 class="font-semibold text-foreground truncate">
                                    {{ getOtraEmpresa(chat) }}
                                </h3>
                                <Badge variant="outline"
                                    class="bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 text-[10px] shrink-0">
                                    {{ chat.estado }}
                                </Badge>
                            </div>
                            <p class="text-xs text-muted-foreground truncate">
                                Material: <strong>{{ chat.publicacion?.nombre || 'Material' }}</strong>
                            </p>
                            <p class="text-xs text-muted-foreground truncate italic mt-0.5">
                                "{{ chat.mensajes?.[0]?.contenido || chat.mensaje }}"
                            </p>
                        </div>

                        <!-- Flecha / acción -->
                        <div class="shrink-0 flex items-center gap-2">
                            <span
                                class="text-xs text-muted-foreground hidden sm:inline group-hover:text-foreground transition-colors">
                                Abrir chat
                            </span>
                            <ArrowRight
                                class="h-4 w-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                        </div>
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</template>