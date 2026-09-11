<script setup lang="ts">
import { ref, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Send, CheckCircle2, X } from 'lucide-vue-next';

const props = defineProps<{
    solicitud: any;
    mensajes: any[];
    empresaId: number;
}>();

const page = usePage();
const message = (page.props.flash as any)?.message ?? null;
const error = (page.props.errors as any)?.error ?? null;

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');


onMounted(() => {
    if (message) {
        toastMessage.value = message;
        toastType.value = 'success';
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
    if (error) {
        toastMessage.value = error;
        toastType.value = 'error';
        showToast.value = true;
        setTimeout(() => { showToast.value = false; }, 3000);
    }
    scrollToBottom();
});

const newMessage = ref('');
const mensajesContainer = ref<HTMLElement | null>(null);

function scrollToBottom() {
    nextTick(() => {
        if (mensajesContainer.value) {
            mensajesContainer.value.scrollTop = mensajesContainer.value.scrollHeight;
        }
    });
}

function enviarMensaje() {
    if (!newMessage.value.trim()) return;
    router.post(`/chat/${props.solicitud.idsolicitud}`, { contenido: newMessage.value }, {
        preserveScroll: true,
        onSuccess: () => {
            newMessage.value = '';
            router.reload(); 
        },
        onError: (errors) => {
            const msg = Object.values(errors).flat().join('\n');
            toastMessage.value = 'Error: ' + msg;
            toastType.value = 'error';
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 3000);
        }
    });
}

// Auto-refresh cada 5 segundos
let interval: number;
onMounted(() => {
    interval = setInterval(() => {
        router.reload(); // ← corregido
    }, 5000);
});

onBeforeUnmount(() => {
    clearInterval(interval);
});
</script>

<template>

    <Head title="Chat" />

    <div class="p-6 bg-background min-h-screen text-foreground">
        <!-- Toast -->
        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px] scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-10px] scale-95">
            <div v-if="showToast"
                class="fixed top-6 right-6 z-[9999] w-[360px] rounded-xl border border-border bg-card shadow-2xl overflow-hidden">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                        :class="toastType === 'success' ? 'bg-green-500/10' : 'bg-red-500/10'">
                        <CheckCircle2 class="h-5 w-5"
                            :class="toastType === 'success' ? 'text-green-500' : 'text-red-500'" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-foreground">{{ toastType === 'success' ? 'Acción completada' :
                            'Error' }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ toastMessage }}</p>
                    </div>
                    <button type="button" @click="showToast = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
                <div class="h-1 bg-muted">
                    <div class="h-full" :class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'"
                        :style="{ animation: 'toast-progress 3s linear forwards' }">
                    </div>
                </div>
            </div>
        </Transition>

        <div class="max-w-4xl mx-auto">
            <Card>
                <CardHeader>
                    <CardTitle class="flex justify-between items-center">
                        <span>💬 Chat - {{ solicitud.publicacion?.nombre || 'Intercambio' }}</span>
                        <span class="text-sm font-normal text-muted-foreground">
                            {{ solicitud.empresa_origen?.nombreEmpresa || 'Empresa 1' }}
                            ↔
                            {{ solicitud.empresa_destino?.nombreEmpresa || 'Empresa 2' }}
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Mensajes -->
                    <div ref="mensajesContainer"
                        class="h-96 overflow-y-auto border border-border rounded-md p-4 mb-4 bg-muted/10">
                        <div v-if="mensajes.length === 0" class="text-center text-muted-foreground py-8">
                            No hay mensajes. ¡Envía el primero!
                        </div>
                        <div v-for="msg in mensajes" :key="msg.idmensajes" class="mb-3">
                            <div class="flex items-start gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-sm">
                                            {{ msg.emisora?.nombreEmpresa || 'Empresa' }}
                                        </span>
                                        <span class="text-xs text-muted-foreground">
                                            {{ new Date(msg.created_at).toLocaleString() }}
                                        </span>
                                    </div>
                                    <p class="text-sm bg-card p-2 rounded-md border border-border mt-1">
                                        {{ msg.contenido }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="flex gap-2">
                        <Input v-model="newMessage" placeholder="Escribe un mensaje..." @keyup.enter="enviarMensaje"
                            class="flex-1" />
                        <Button @click="enviarMensaje" class="bg-primary text-primary-foreground">
                            <Send class="w-4 h-4 mr-2" />
                            Enviar
                        </Button>
                    </div>
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