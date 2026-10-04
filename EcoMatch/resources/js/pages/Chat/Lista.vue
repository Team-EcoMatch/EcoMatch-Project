<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { MessageSquare, ArrowRight, Building, Search, X, CheckCheck } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    chats: any[];
    empresaId: number;
}>();

const searchQuery = ref('');

const filteredChats = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.chats;
    return props.chats.filter(chat => {
        const empresa = getOtraEmpresa(chat).toLowerCase();
        const material = (chat.publicacion?.nombre || '').toLowerCase();
        const mensaje = (chat.mensajes?.[0]?.contenido || chat.mensaje || '').toLowerCase();
        return empresa.includes(query) || material.includes(query) || mensaje.includes(query);
    });
});

function getOtraEmpresa(chat: any): string {
    const esOrigen = chat.idEmpresaOrigen === props.empresaId;
    const otra = esOrigen ? (chat.empresa_destino || chat.empresaDestino) : (chat.empresa_origen || chat.empresaOrigen);
    return otra?.nombreEmpresa || 'Empresa';
}

function getAvatarColor(nombre: string): { gradient: string; text: string } {
    const colors = [
        { gradient: 'from-blue-500/20 to-blue-500/5 border-blue-500/20', text: 'text-blue-500' },
        { gradient: 'from-emerald-500/20 to-emerald-500/5 border-emerald-500/20', text: 'text-emerald-500' },
        { gradient: 'from-amber-500/20 to-amber-500/5 border-amber-500/20', text: 'text-amber-500' },
        { gradient: 'from-purple-500/20 to-purple-500/5 border-purple-500/20', text: 'text-purple-500' },
        { gradient: 'from-pink-500/20 to-pink-500/5 border-pink-500/20', text: 'text-pink-500' },
        { gradient: 'from-indigo-500/20 to-indigo-500/5 border-indigo-500/20', text: 'text-indigo-500' },
        { gradient: 'from-teal-500/20 to-teal-500/5 border-teal-500/20', text: 'text-teal-500' },
    ];
    const hash = nombre.charCodeAt(0) % colors.length;
    return colors[hash];
}

function isUnread(chat: any): boolean {
    const lastMsg = chat.mensajes?.[0];
    if (!lastMsg) return false;
    return !lastMsg.leido && lastMsg.idEmisora !== props.empresaId;
}

function timeAgo(date: string): string {
    if (!date) return '';
    const now = new Date();
    const past = new Date(date);
    const diff = now.getTime() - past.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const mins = Math.floor(diff / (1000 * 60));
    if (days > 7) return past.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
    if (days > 0) return `Hace ${days}d`;
    if (hours > 0) return `Hace ${hours}h`;
    if (mins > 0) return `Hace ${mins}min`;
    return 'Ahora';
}

function getLastMessageTime(chat: any): string {
    const lastMsg = chat.mensajes?.[0];
    if (lastMsg?.created_at) return timeAgo(lastMsg.created_at);
    if (chat.updated_at) return timeAgo(chat.updated_at);
    return '';
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">

        <Head title="Mis Chats" />

        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <MessageSquare class="w-6 h-6 text-primary" />
                    Conversaciones de Intercambio
                </h2>
                <p class="text-sm text-muted-foreground mt-1">
                    Chats activos con las empresas con las que tienes solicitudes aceptadas.
                </p>
            </div>

            <div v-if="chats.length > 0" class="relative mb-6">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <input v-model="searchQuery" type="text" placeholder="Buscar por empresa, material o mensaje..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-input bg-background text-sm text-foreground placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-0 outline-none" />
            </div>

            <div v-if="chats.length === 0"
                class="text-center py-16 border border-dashed rounded-xl text-muted-foreground bg-card">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mx-auto mb-4">
                    <MessageSquare class="w-8 h-8 text-primary/50" />
                </div>
                <p class="text-lg font-medium">No tienes chats activos</p>
                <p class="text-sm mt-1">Los chats se activan automáticamente cuando una solicitud es aceptada.</p>
            </div>

            <div v-else-if="filteredChats.length === 0"
                class="text-center py-16 border border-dashed rounded-xl text-muted-foreground bg-card">
                <Search class="w-12 h-12 mx-auto mb-3 opacity-30" />
                <p class="text-lg font-medium">No se encontraron chats</p>
                <p class="text-sm mt-1">Prueba con otro término de búsqueda</p>
            </div>

            <Card v-else class="overflow-hidden">
                <div class="divide-y divide-border">
                    <Link v-for="chat in filteredChats" :key="chat.idsolicitud" :href="`/chat/${chat.idsolicitud}`"
                        class="flex items-center gap-4 p-4 hover:bg-accent transition-colors group relative">

                        <div v-if="isUnread(chat)" class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>

                        <div
                            :class="['flex', 'h-12', 'w-12', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(getOtraEmpresa(chat)).gradient]">
                            <span :class="['text-lg', 'font-bold', getAvatarColor(getOtraEmpresa(chat)).text]">
                                {{ getOtraEmpresa(chat).charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <h3 class="font-semibold text-foreground truncate">
                                        {{ getOtraEmpresa(chat) }}
                                    </h3>
                                    <span v-if="isUnread(chat)" class="h-2 w-2 rounded-full bg-primary shrink-0"></span>
                                </div>
                                <span class="text-xs text-muted-foreground shrink-0">
                                    {{ getLastMessageTime(chat) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2 mb-1">
                                <Badge variant="outline"
                                    :class="chat.estado === 'Completado'
                                        ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'
                                        : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'"
                                    class="text-[10px] shrink-0">
                                    {{ chat.estado }}
                                </Badge>
                                <p class="text-xs text-muted-foreground truncate">
                                    {{ chat.publicacion?.nombre || 'Material' }}
                                </p>
                            </div>

                            <p class="text-sm text-muted-foreground truncate"
                                :class="isUnread(chat) ? 'font-medium text-foreground' : ''">
                                {{ chat.mensajes?.[0]?.contenido || chat.mensaje || 'Sin mensajes aún' }}
                            </p>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            <CheckCheck v-if="chat.mensajes?.[0]?.leido && chat.mensajes?.[0]?.idEmisora === empresaId"
                                class="w-4 h-4 text-blue-500" />
                            <ArrowRight
                                class="h-4 w-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
                        </div>
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</template>