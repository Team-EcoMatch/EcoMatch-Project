<script setup lang="ts">
import { ref, watch, onMounted, nextTick, onBeforeUnmount, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Send, CheckCircle2, X, Paperclip, Ban, Unlock, FileText, Download } from 'lucide-vue-next';

const props = defineProps<{
    solicitud: any;
    mensajes: any[];
    empresaId: number;
    empresaNombre: string;
    bloqueadoPorMi: boolean;
    bloqueadoHaciaMi: boolean;
    cloudName: string;
    uploadPreset: string;
}>();

const page = usePage();
const message = (page.props.flash as any)?.message ?? null;
const error = (page.props.errors as any)?.error ?? null;

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
const toastTimer = ref<number | null>(null);

const bloqueadoPorMi = ref(props.bloqueadoPorMi);
const bloqueadoHaciaMi = ref(props.bloqueadoHaciaMi);

watch(() => props.bloqueadoPorMi, (v) => { bloqueadoPorMi.value = v; });
watch(() => props.bloqueadoHaciaMi, (v) => { bloqueadoHaciaMi.value = v; });

const mensajes = ref<any[]>([...props.mensajes]);
const newMessage = ref('');
const mensajesContainer = ref<HTMLElement | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);
const uploadProgress = ref(0);

const noPuedeEnviarMensajes = computed(() => bloqueadoHaciaMi.value);

function esMio(msg: any): boolean {
    return msg.idEmisora === props.empresaId;
}

function scrollToBottom() {
    nextTick(() => {
        if (mensajesContainer.value) {
            mensajesContainer.value.scrollTop = mensajesContainer.value.scrollHeight;
        }
    });
}

watch(
    () => props.mensajes,
    (nuevosMensajes) => {
        const temporales = mensajes.value.filter(m =>
            String(m.idmensajes).startsWith('temp-') &&
            !nuevosMensajes.some(nm =>
                nm.archivo_url === m.archivo_url ||
                (nm.contenido === m.contenido && nm.idEmisora === m.idEmisora)
            )
        );
        mensajes.value = [...nuevosMensajes, ...temporales];
        scrollToBottom();
    },
    { deep: true }
);

function mostrarToast(msg: string, type: 'success' | 'error', duracion: number = 3000) {
    if (toastTimer.value) {
        clearTimeout(toastTimer.value);
    }
    toastMessage.value = msg;
    toastType.value = type;
    showToast.value = true;
    toastTimer.value = window.setTimeout(() => {
        showToast.value = false;
    }, duracion);
}

onMounted(() => {
    if (message) {
        mostrarToast(message, 'success');
    }
    if (error) {
        mostrarToast(error, 'error');
    }

    scrollToBottom();

    if (typeof window !== 'undefined' && window.Echo) {
        window.Echo.private(`chat.${props.solicitud.idsolicitud}`)
            .listen('.mensaje.enviado', (e: any) => {
                const existe = mensajes.value.some(m =>
                    m.idmensajes === e.mensaje.idmensajes ||
                    (String(m.idmensajes).startsWith('temp-') &&
                        m.contenido === e.mensaje.contenido &&
                        m.idEmisora === e.mensaje.idEmisora)
                );
                if (!existe) {
                    mensajes.value.push(e.mensaje);
                    scrollToBottom();
                }
            })
            .listen('.empresa.bloqueada', (data: any) => {
                if (data.idempresa_bloqueada === props.empresaId) {
                    bloqueadoHaciaMi.value = true;
                    mostrarToast(
                        `🚫 ${data.nombreBloqueadora} te ha bloqueado. Ya no puedes enviar mensajes.`,
                        'error',
                        5000
                    );
                    newMessage.value = '';
                }
            })
            .listen('.empresa.desbloqueada', (data: any) => {
                if (data.idempresa_bloqueada === props.empresaId) {
                    bloqueadoHaciaMi.value = false;
                    mostrarToast(
                        `✅ ${data.nombreBloqueadora} te ha desbloqueado. Ya puedes enviar mensajes.`,
                        'success',
                        5000
                    );
                }
            });
    }
});

onBeforeUnmount(() => {
    if (toastTimer.value) {
        clearTimeout(toastTimer.value);
    }
    if (typeof window !== 'undefined' && window.Echo) {
        window.Echo.leave(`chat.${props.solicitud.idsolicitud}`);
    }
});

async function subirACloudinary(file: File): Promise<string> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', props.uploadPreset);
    formData.append('folder', `chat/${props.solicitud.idsolicitud}`);

    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `https://api.cloudinary.com/v1_1/${props.cloudName}/auto/upload`);

        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                uploadProgress.value = Math.round((e.loaded / e.total) * 100);
            }
        };

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                const response = JSON.parse(xhr.responseText);
                resolve(response.secure_url);
            } else {
                reject(new Error('Error subiendo archivo'));
            }
        };

        xhr.onerror = () => reject(new Error('Error de red'));
        xhr.send(formData);
    });
}

function crearMensajeLocal(datos: {
    contenido: string;
    tipo: string;
    archivo_url?: string;
    archivo_nombre?: string;
    archivo_tamano?: number;
}): any {
    return {
        idmensajes: 'temp-' + Date.now(),
        idEmisora: props.empresaId,
        contenido: datos.contenido,
        tipo: datos.tipo,
        archivo_url: datos.archivo_url || null,
        archivo_nombre: datos.archivo_nombre || null,
        archivo_tamano: datos.archivo_tamano || null,
        created_at: new Date().toISOString(),
        empresa_emisora: {
            idempresa: props.empresaId,
            nombreEmpresa: props.empresaNombre
        }
    };
}

async function onFileSelected(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    if (noPuedeEnviarMensajes.value) {
        mostrarToast('No puedes enviar archivos. Fuiste bloqueado.', 'error');
        if (fileInput.value) fileInput.value.value = '';
        return;
    }

    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        mostrarToast('El archivo no puede pesar más de 10MB', 'error');
        if (fileInput.value) fileInput.value.value = '';
        return;
    }

    uploading.value = true;
    uploadProgress.value = 0;

    try {
        const secure_url = await subirACloudinary(file);
        const tipo = file.type.startsWith('image/') ? 'imagen' : 'archivo';

        const mensajeLocal = crearMensajeLocal({
            contenido: '',
            tipo,
            archivo_url: secure_url,
            archivo_nombre: file.name,
            archivo_tamano: file.size,
        });
        mensajes.value.push(mensajeLocal);
        scrollToBottom();

        router.post(`/chat/${props.solicitud.idsolicitud}`, {
            contenido: '',
            tipo,
            archivo_url: secure_url,
            archivo_nombre: file.name,
            archivo_tamano: file.size,
        }, {
            preserveScroll: true,
            onError: (errors) => {
                mensajes.value = mensajes.value.filter(m => m.idmensajes !== mensajeLocal.idmensajes);
                mostrarToast('Error: ' + Object.values(errors).join(', '), 'error');
            }
        });
    } catch (err) {
        mostrarToast('Error al subir el archivo', 'error');
    } finally {
        uploading.value = false;
        uploadProgress.value = 0;
        if (fileInput.value) fileInput.value.value = '';
    }
}

function enviarMensaje() {
    if (noPuedeEnviarMensajes.value) {
        mostrarToast('No puedes enviar mensajes. Fuiste bloqueado.', 'error');
        return;
    }
    if (!newMessage.value.trim()) return;

    const contenido = newMessage.value;
    newMessage.value = '';

    const mensajeLocal = crearMensajeLocal({
        contenido,
        tipo: 'texto',
    });
    mensajes.value.push(mensajeLocal);
    scrollToBottom();

    router.post(`/chat/${props.solicitud.idsolicitud}`, {
        contenido,
        tipo: 'texto'
    }, {
        preserveScroll: true,
        onError: (errors) => {
            mensajes.value = mensajes.value.filter(m => m.idmensajes !== mensajeLocal.idmensajes);
            mostrarToast('Error: ' + Object.values(errors).flat().join('\n'), 'error');
            newMessage.value = contenido;
        }
    });
}

function bloquearEmpresa() {
    router.post(`/chat/${props.solicitud.idsolicitud}/bloquear`, {}, {
        preserveScroll: true,
    });
}

function desbloquearEmpresa() {
    router.post(`/chat/${props.solicitud.idsolicitud}/desbloquear`, {}, {
        preserveScroll: true,
    });
}

function formatSize(bytes: number): string {
    if (!bytes) return '0 B';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1024 / 1024).toFixed(1) + ' MB';
}

function getExtension(nombre: string): string {
    return nombre.split('.').pop()?.toUpperCase() || 'FILE';
}

function abrirImagen(url: string) {
    if (typeof window !== 'undefined') {
        window.open(url, '_blank');
    }
}
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">
        <Head title="Chat" />

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
                        <p class="font-semibold text-foreground">{{ toastType === 'success' ? 'Acción completada' : 'Aviso' }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ toastMessage }}</p>
                    </div>
                    <button type="button" @click="showToast = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>

        <div class="max-w-4xl mx-auto">
            <Card>
                <CardHeader>
                    <CardTitle class="flex justify-between items-center">
                        <span class="text-lg">💬 Chat - {{ solicitud.publicacion?.nombre || 'Intercambio' }}</span>

                        <AlertDialog v-if="!bloqueadoPorMi">
                            <AlertDialogTrigger as-child>
                                <Button variant="ghost" size="sm" class="text-red-600 hover:text-red-700">
                                    <Ban class="w-4 h-4 mr-1" />
                                    Bloquear
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent class="bg-card border-border">
                                <AlertDialogHeader>
                                    <AlertDialogTitle>¿Bloquear empresa?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        La empresa no podrá enviarte mensajes. Puedes desbloquearla cuando quieras.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                    <AlertDialogAction @click="bloquearEmpresa"
                                        class="bg-red-600 hover:bg-red-700 text-white">
                                        Sí, bloquear
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>

                        <Button v-else variant="ghost" size="sm" @click="desbloquearEmpresa"
                            class="text-green-600 hover:text-green-700">
                            <Unlock class="w-4 h-4 mr-1" />
                            Desbloquear
                        </Button>
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    <div v-if="bloqueadoHaciaMi"
                        class="mb-4 p-3 rounded-md bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 text-sm text-center border border-amber-200 dark:border-amber-800">
                        <Ban class="w-4 h-4 inline mr-1" />
                        Esta empresa te ha bloqueado. No puedes enviar mensajes.
                    </div>
                    <div v-else-if="bloqueadoPorMi"
                        class="mb-4 p-3 rounded-md bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 text-sm text-center border border-blue-200 dark:border-blue-800">
                        Has bloqueado a esta empresa. Desbloquéala para reanudar la conversación.
                    </div>

                    <div ref="mensajesContainer"
                        class="h-96 overflow-y-auto border border-border rounded-md p-4 mb-4 bg-muted/10">
                        <div v-if="mensajes.length === 0" class="text-center text-muted-foreground py-8">
                            No hay mensajes. ¡Envía el primero!
                        </div>

                        <div v-for="msg in mensajes" :key="msg.idmensajes" class="mb-3 flex"
                            :class="esMio(msg) ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[75%]">
                                <div class="text-xs mb-1"
                                    :class="esMio(msg) ? 'text-right text-muted-foreground' : 'text-left text-muted-foreground'">
                                    <strong>{{ msg.empresa_emisora?.nombreEmpresa || 'Empresa' }}</strong>
                                    <span class="ml-1">
                                        · {{ new Date(msg.created_at).toLocaleTimeString([], {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) }}
                                    </span>
                                </div>

                                <div class="px-3 py-2 rounded-2xl shadow-sm text-sm break-words border border-border"
                                    :class="esMio(msg)
                                        ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/50 dark:text-emerald-100 rounded-br-sm'
                                        : 'bg-muted text-foreground dark:bg-muted/70 rounded-bl-sm'">

                                    <div v-if="msg.tipo === 'imagen'">
                                        <img :src="msg.archivo_url" :alt="msg.archivo_nombre"
                                            class="rounded-lg max-w-full max-h-72 object-cover mb-2 cursor-pointer"
                                            @click="abrirImagen(msg.archivo_url)" />
                                        <p v-if="msg.contenido" class="mt-1">{{ msg.contenido }}</p>
                                    </div>

                                    <div v-else-if="msg.tipo === 'archivo'"
                                        class="flex items-center gap-3 min-w-[200px]">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                            <FileText class="h-5 w-5 text-primary" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium truncate">{{ msg.archivo_nombre }}</p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ getExtension(msg.archivo_nombre) }} · {{ formatSize(msg.archivo_tamano || 0) }}
                                            </p>
                                        </div>
                                        <a :href="msg.archivo_url" target="_blank" download
                                            class="text-primary hover:text-primary/80">
                                            <Download class="w-4 h-4" />
                                        </a>
                                    </div>

                                    <p v-else>{{ msg.contenido }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="uploading" class="mb-2">
                        <div class="h-1 bg-muted rounded-full overflow-hidden">
                            <div class="h-full bg-primary transition-all"
                                :style="{ width: uploadProgress + '%' }"></div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 text-center">
                            Subiendo... {{ uploadProgress }}%
                        </p>
                    </div>

                    <div class="flex gap-2 items-center">
                        <input ref="fileInput" type="file" class="hidden" @change="onFileSelected"
                            accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" />
                        <Button variant="ghost" size="icon" @click="fileInput?.click()"
                            :disabled="uploading || noPuedeEnviarMensajes" class="shrink-0"
                            title="Adjuntar archivo">
                            <Paperclip class="w-5 h-5" />
                        </Button>

                        <Input v-model="newMessage" placeholder="Escribe un mensaje..."
                            @keyup.enter="enviarMensaje" :disabled="noPuedeEnviarMensajes"
                            class="flex-1" />

                        <Button @click="enviarMensaje"
                            :disabled="!newMessage.trim() || noPuedeEnviarMensajes"
                            class="bg-primary text-primary-foreground shrink-0">
                            <Send class="w-4 h-4" />
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>