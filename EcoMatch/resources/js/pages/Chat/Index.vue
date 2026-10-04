<script setup lang="ts">
import { ref, watch, onMounted, nextTick, onBeforeUnmount, computed } from 'vue';
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Dialog, DialogContent, DialogDescription, DialogFooter,
    DialogHeader, DialogTitle, DialogTrigger,
} from '@/components/ui/dialog';
import { Send, CheckCircle2, X, Paperclip, Ban, Unlock, FileText, Download, Check, CheckCheck, ArrowLeft, MessageCircle } from 'lucide-vue-next';

const props = defineProps<{
    solicitud: any;
    mensajes: any[];
    empresaId: number;
    empresaNombre: string;
    bloqueadoPorMi: boolean;
    bloqueadoHaciaMi: boolean;
    motivoBloqueo: string | null;
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

const showBloqueoModal = ref(false);
const motivoBloqueoInput = ref('');

const mensajes = ref<any[]>([...props.mensajes]);
const newMessage = ref('');
const mensajesContainer = ref<HTMLElement | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);
const uploadProgress = ref(0);

const noPuedeEnviarMensajes = computed(() => bloqueadoHaciaMi.value);

const ARCHIVOS_PERMITIDOS = {
    imagenes: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    documentos: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
    comprimidos: ['zip', 'rar', '7z'],
};

const EXTENSIONES_PROHIBIDAS = [
    'exe', 'msi', 'bat', 'cmd', 'sh', 'js', 'mjs',
    'php', 'phtml', 'php3', 'php4', 'php5',
    'py', 'rb', 'pl', 'cgi',
    'apk', 'jar', 'class', 'dll', 'so', 'dylib',
    'vbs', 'ps1', 'scr', 'com',
    'html', 'htm', 'svg',
];

const TAMANOS_MAXIMOS = {
    imagen: 5 * 1024 * 1024,
    documento: 10 * 1024 * 1024,
    comprimido: 10 * 1024 * 1024,
    texto: 2 * 1024 * 1024,
};

const MAGIC_BYTES: Record<string, { pattern: string; tipos: string[] }> = {
    pdf: { pattern: '25 50 44 46 2d', tipos: ['pdf'] },
    png: { pattern: '89 50 4e 47 0d 0a 1a 0a', tipos: ['png'] },
    jpg: { pattern: 'ff d8 ff', tipos: ['jpg', 'jpeg'] },
    gif: { pattern: '47 49 46 38', tipos: ['gif'] },
    webp: { pattern: '52 49 46 46', tipos: ['webp'] },
    zip: { pattern: '50 4b 03 04', tipos: ['zip', 'docx', 'xlsx', 'pptx'] },
    '7z': { pattern: '37 7a bc af 27 1c', tipos: ['7z'] },
    rar4: { pattern: '52 61 72 21 1a 07 00', tipos: ['rar'] },
    rar5: { pattern: '52 61 72 21 1a 07 01 00', tipos: ['rar'] },
    office_legacy: { pattern: 'd0 cf 11 e0 a1 b1 1a e1', tipos: ['doc', 'xls', 'ppt'] },
    exe: { pattern: '4d 5a', tipos: ['exe'] },
    elf: { pattern: '7f 45 4c 46', tipos: ['elf'] },
    macho: { pattern: 'cf fa ed fe', tipos: ['macho'] },
    java_class: { pattern: 'ca fe ba be', tipos: ['class'] },
};

function esMio(msg: any): boolean {
    return msg.idEmisora === props.empresaId;
}

function getOtraEmpresa(): string {
    const esOrigen = props.solicitud.idEmpresaOrigen === props.empresaId;
    const otra = esOrigen ? props.solicitud.empresa_destino : props.solicitud.empresa_origen;
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

function getDateLabel(date: string): string {
    if (!date) return '';
    const msgDate = new Date(date);
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    if (msgDate.toDateString() === today.toDateString()) return 'Hoy';
    if (msgDate.toDateString() === yesterday.toDateString()) return 'Ayer';
    return msgDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
}

const mensajesAgrupados = computed(() => {
    const groups: { fecha: string; mensajes: any[] }[] = [];
    let currentDate = '';
    mensajes.value.forEach(msg => {
        const dateLabel = getDateLabel(msg.created_at);
        if (dateLabel !== currentDate) {
            currentDate = dateLabel;
            groups.push({ fecha: dateLabel, mensajes: [] });
        }
        groups[groups.length - 1].mensajes.push(msg);
    });
    return groups;
});

function scrollToBottom() {
    nextTick(() => {
        if (mensajesContainer.value) {
            mensajesContainer.value.scrollTo({
                top: mensajesContainer.value.scrollHeight,
                behavior: 'smooth'
            });
        }
    });
}

watch(
    () => props.mensajes,
    (nuevosMensajes) => {
        const temporales = mensajes.value.filter(m =>
            String(m.idmensajes).startsWith('temp-') &&
            !nuevosMensajes.some(nm =>
                (m.archivo_url && nm.archivo_url === m.archivo_url) ||
                (m.contenido === nm.contenido && m.idEmisora === nm.idEmisora)
            )
        );
        mensajes.value = [...nuevosMensajes, ...temporales];
        scrollToBottom();
    },
    { deep: true, flush: 'sync' }
);

function mostrarToast(msg: string, type: 'success' | 'error', duracion: number = 3000) {
    if (toastTimer.value) clearTimeout(toastTimer.value);
    toastMessage.value = msg;
    toastType.value = type;
    showToast.value = true;
    toastTimer.value = window.setTimeout(() => { showToast.value = false; }, duracion);
}

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getFetchHeaders(): Record<string, string> {
    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
    };
    if (typeof window !== 'undefined' && window.Echo?.socketId()) {
        headers['X-Socket-Id'] = window.Echo.socketId();
    }
    return headers;
}

function getDownloadUrl(url: string, fileName: string): string {
    if (!url) return '';
    const ext = fileName.split('.').pop()?.toLowerCase() || '';
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
    if (!isImage && url.includes('/image/upload/')) {
        return url.replace('/image/upload/', '/raw/upload/');
    }
    return url;
}

function leerMagicBytes(file: File, numBytes: number = 12): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const buffer = e.target?.result as ArrayBuffer;
            if (!buffer) { reject(new Error('No se pudo leer el archivo')); return; }
            const bytes = new Uint8Array(buffer);
            const hex = Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join(' ');
            resolve(hex);
        };
        reader.onerror = () => reject(new Error('Error leyendo el archivo'));
        reader.readAsArrayBuffer(file.slice(0, numBytes));
    });
}

async function detectarTipoReal(file: File): Promise<{ tipo: string; confiable: boolean }> {
    try {
        const hexBytes = await leerMagicBytes(file, 12);
        const hexLower = hexBytes.toLowerCase();
        if (hexLower.startsWith('52 49 46 46')) {
            if (hexLower.includes('57 45 42 50')) return { tipo: 'webp', confiable: true };
            return { tipo: 'riff-otro', confiable: false };
        }
        for (const [key, info] of Object.entries(MAGIC_BYTES)) {
            if (key === 'webp') continue;
            if (hexLower.startsWith(info.pattern)) {
                if (key === 'zip') return { tipo: 'zip_family', confiable: true };
                return { tipo: key, confiable: true };
            }
        }
        return { tipo: 'desconocido', confiable: false };
    } catch (err) {
        return { tipo: 'error_lectura', confiable: false };
    }
}

async function validarConsistencia(file: File, extension: string): Promise<{ valido: boolean; error?: string }> {
    const deteccion = await detectarTipoReal(file);
    const tiposPeligrosos = ['exe', 'elf', 'macho', 'java_class', 'riff-otro'];
    if (extension === 'txt' || extension === 'csv') {
        if (tiposPeligrosos.includes(deteccion.tipo)) {
            return { valido: false, error: `El archivo dice ser .${extension} pero su contenido real es un ejecutable. Archivo peligroso.` };
        }
        return { valido: true };
    }
    const extensionATipo: Record<string, string> = {
        pdf: 'pdf', png: 'png', jpg: 'jpg', jpeg: 'jpg', gif: 'gif', webp: 'webp',
        zip: 'zip_family', '7z': '7z', rar: 'rar4', docx: 'zip_family', xlsx: 'zip_family',
        pptx: 'zip_family', doc: 'office_legacy', xls: 'office_legacy', ppt: 'office_legacy',
    };
    const tipoEsperado = extensionATipo[extension];
    if (!tipoEsperado) return { valido: false, error: `No se reconoce la extensión .${extension}` };
    if (deteccion.tipo === tipoEsperado) return { valido: true };
    if (tipoEsperado === 'zip_family' && deteccion.tipo === 'zip_family') return { valido: true };
    if (tipoEsperado === 'office_legacy' && deteccion.tipo === 'office_legacy') return { valido: true };
    if (extension === 'rar' && (deteccion.tipo === 'rar4' || deteccion.tipo === 'rar5')) return { valido: true };
    if (tiposPeligrosos.includes(deteccion.tipo)) {
        return { valido: false, error: `El archivo dice ser .${extension} pero su contenido real es un ejecutable. Archivo peligroso.` };
    }
    return { valido: false, error: `El archivo dice ser .${extension} pero su contenido real es "${deteccion.tipo}". Posible archivo peligroso.` };
}

function validarMimeType(file: File, extension: string): boolean {
    const mime = file.type.toLowerCase();
    const mimeEsperado: Record<string, string[]> = {
        jpg: ['image/jpeg', 'image/jpg'], jpeg: ['image/jpeg', 'image/jpg'],
        png: ['image/png'], gif: ['image/gif'], webp: ['image/webp'],
        pdf: ['application/pdf'], doc: ['application/msword'],
        docx: ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        xls: ['application/vnd.ms-excel'],
        xlsx: ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ppt: ['application/vnd.ms-powerpoint'],
        pptx: ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        txt: ['text/plain'], csv: ['text/csv', 'text/plain', 'application/vnd.ms-excel'],
        zip: ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'],
        rar: ['application/x-rar-compressed', 'application/vnd.rar', 'application/octet-stream'],
        '7z': ['application/x-7z-compressed', 'application/octet-stream'],
    };
    if (!mime || mime === 'application/octet-stream') return true;
    const esperado = mimeEsperado[extension];
    if (!esperado) return true;
    return esperado.includes(mime);
}

async function validarArchivo(file: File): Promise<{ valido: boolean; error?: string; tipo?: string }> {
    if (!file.name) return { valido: false, error: 'El archivo no es válido.' };
    const extension = file.name.split('.').pop()?.toLowerCase() || '';
    if (!extension) return { valido: false, error: 'El archivo no tiene extensión.' };
    if (EXTENSIONES_PROHIBIDAS.includes(extension)) return { valido: false, error: `La extensión ".${extension}" no está permitida por seguridad.` };
    let tipo = ''; let tamanoMaximo = 0;
    if (ARCHIVOS_PERMITIDOS.imagenes.includes(extension)) { tipo = 'imagen'; tamanoMaximo = TAMANOS_MAXIMOS.imagen; }
    else if (ARCHIVOS_PERMITIDOS.documentos.includes(extension)) {
        if (['txt', 'csv'].includes(extension)) { tipo = 'texto'; tamanoMaximo = TAMANOS_MAXIMOS.texto; }
        else { tipo = 'documento'; tamanoMaximo = TAMANOS_MAXIMOS.documento; }
    } else if (ARCHIVOS_PERMITIDOS.comprimidos.includes(extension)) { tipo = 'comprimido'; tamanoMaximo = TAMANOS_MAXIMOS.comprimido; }
    else return { valido: false, error: `La extensión ".${extension}" no está permitida.` };
    if (file.size > tamanoMaximo) {
        const mb = (tamanoMaximo / (1024 * 1024)).toFixed(0);
        return { valido: false, error: `El archivo pesa ${formatSize(file.size)}. El máximo para ${tipo}s es ${mb}MB.` };
    }
    if (file.name.length > 255) return { valido: false, error: 'El nombre del archivo es demasiado largo.' };
    const consistencia = await validarConsistencia(file, extension);
    if (!consistencia.valido) return { valido: false, error: consistencia.error || 'El archivo no coincide con su extensión.' };
    const mimeValido = validarMimeType(file, extension);
    if (!mimeValido) return { valido: false, error: `El tipo MIME del archivo no coincide con su extensión.` };
    return { valido: true, tipo: tipo === 'imagen' ? 'imagen' : 'archivo' };
}

function marcarComoLeido() {
    fetch(`/chat/${props.solicitud.idsolicitud}/marcar-leido`, {
        method: 'POST', headers: getFetchHeaders(), body: JSON.stringify({}),
    }).catch(() => { });
}

onMounted(() => {
    if (message) mostrarToast(message, 'success');
    if (error) mostrarToast(error, 'error');
    scrollToBottom();
    if (typeof window !== 'undefined' && window.Echo) {
        window.Echo.private(`chat.${props.solicitud.idsolicitud}`)
            .listen('.mensaje.enviado', (e: any) => {
                const existe = mensajes.value.some(m => m.idmensajes === e.mensaje.idmensajes);
                if (!existe) { mensajes.value.push(e.mensaje); scrollToBottom(); marcarComoLeido(); }
            })
            .listen('.mensaje.leido', (data: any) => {
                data.idsMensajes.forEach((id: number) => {
                    const msg = mensajes.value.find(m => m.idmensajes === id);
                    if (msg) msg.leido = true;
                });
            });
        window.Echo.private(`empresa.${props.empresaId}`)
            .listen('.empresa.bloqueada', (data: any) => {
                if (data.idempresa_bloqueada === props.empresaId) {
                    bloqueadoHaciaMi.value = true;
                    const motivoTexto = data.motivo && data.motivo !== 'Sin especificar' ? `\nMotivo: ${data.motivo}` : '';
                    mostrarToast(`🚫 ${data.nombreBloqueadora} te ha bloqueado.${motivoTexto}`, 'error', 8000);
                    newMessage.value = '';
                }
            })
            .listen('.empresa.desbloqueada', (data: any) => {
                if (data.idempresa_bloqueada === props.empresaId) {
                    bloqueadoHaciaMi.value = false;
                    mostrarToast(`✅ ${data.nombreBloqueadora} te ha desbloqueado.`, 'success', 5000);
                }
            });
    }
});

onBeforeUnmount(() => {
    if (toastTimer.value) clearTimeout(toastTimer.value);
    if (typeof window !== 'undefined' && window.Echo) {
        window.Echo.leave(`chat.${props.solicitud.idsolicitud}`);
        window.Echo.leave(`empresa.${props.empresaId}`);
    }
});

async function subirACloudinary(fileContent: ArrayBuffer, fileName: string, fileType: string): Promise<string> {
    const blob = new Blob([fileContent], { type: fileType });
    const formData = new FormData();
    formData.append('file', blob, fileName);
    formData.append('upload_preset', props.uploadPreset || 'chat_preset');
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `https://api.cloudinary.com/v1_1/${props.cloudName || 'nbwtcdmw'}/auto/upload`);
        xhr.upload.onprogress = (e) => { if (e.lengthComputable) uploadProgress.value = Math.round((e.loaded / e.total) * 100); };
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) { resolve(JSON.parse(xhr.responseText).secure_url); }
            else { reject(new Error('Cloudinary: ' + xhr.responseText)); }
        };
        xhr.onerror = () => reject(new Error('Error de red'));
        xhr.send(formData);
    });
}

function crearMensajeLocal(datos: { contenido: string; tipo: string; archivo_url?: string; archivo_nombre?: string; archivo_tamano?: number; }): any {
    return {
        idmensajes: 'temp-' + Date.now(), idEmisora: props.empresaId, contenido: datos.contenido, tipo: datos.tipo,
        archivo_url: datos.archivo_url || null, archivo_nombre: datos.archivo_nombre || null,
        archivo_tamano: datos.archivo_tamano || null, leido: false, created_at: new Date().toISOString(),
        empresa_emisora: { idempresa: props.empresaId, nombreEmpresa: props.empresaNombre }
    };
}

async function onFileSelected(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;
    if (noPuedeEnviarMensajes.value) { mostrarToast('No puedes enviar archivos. Fuiste bloqueado.', 'error'); if (fileInput.value) fileInput.value.value = ''; return; }
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) { mostrarToast('El archivo no puede pesar más de 10MB', 'error'); if (fileInput.value) fileInput.value.value = ''; return; }
    const fileContent = await file.arrayBuffer();
    const fileName = file.name;
    const fileType = file.type;
    const validacion = await validarArchivo(file);
    if (!validacion.valido) { mostrarToast(validacion.error || 'Archivo no válido', 'error', 5000); if (fileInput.value) fileInput.value.value = ''; return; }
    uploading.value = true; uploadProgress.value = 0;
    let tempId: string | null = null;
    try {
        const secure_url = await subirACloudinary(fileContent, fileName, fileType);
        const tipo = validacion.tipo!;
        const mensajeLocal = crearMensajeLocal({ contenido: '', tipo, archivo_url: secure_url, archivo_nombre: fileName, archivo_tamano: fileContent.byteLength });
        tempId = mensajeLocal.idmensajes;
        mensajes.value.push(mensajeLocal);
        scrollToBottom();
        const res = await fetch(`/chat/${props.solicitud.idsolicitud}`, {
            method: 'POST', headers: getFetchHeaders(),
            body: JSON.stringify({ contenido: '', tipo, archivo_url: secure_url, archivo_nombre: fileName, archivo_tamano: fileContent.byteLength }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Error al enviar');
        const index = mensajes.value.findIndex(m => m.idmensajes === tempId);
        if (index >= 0) mensajes.value[index] = data.mensaje;
    } catch (err: any) {
        if (tempId) mensajes.value = mensajes.value.filter(m => m.idmensajes !== tempId);
        mostrarToast(err.message || 'Error al subir el archivo', 'error');
    } finally {
        uploading.value = false; uploadProgress.value = 0;
        if (fileInput.value) fileInput.value.value = '';
    }
}

async function enviarMensaje() {
    if (noPuedeEnviarMensajes.value) { mostrarToast('No puedes enviar mensajes. Fuiste bloqueado.', 'error'); return; }
    if (!newMessage.value.trim()) return;
    const contenido = newMessage.value;
    newMessage.value = '';
    const mensajeLocal = crearMensajeLocal({ contenido, tipo: 'texto' });
    mensajes.value.push(mensajeLocal);
    scrollToBottom();
    try {
        const res = await fetch(`/chat/${props.solicitud.idsolicitud}`, {
            method: 'POST', headers: getFetchHeaders(),
            body: JSON.stringify({ contenido, tipo: 'texto' }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Error al enviar');
        const index = mensajes.value.findIndex(m => m.idmensajes === mensajeLocal.idmensajes);
        if (index >= 0) mensajes.value[index] = data.mensaje;
    } catch (err: any) {
        mensajes.value = mensajes.value.filter(m => m.idmensajes !== mensajeLocal.idmensajes);
        mostrarToast(err.message || 'Error al enviar el mensaje', 'error');
        newMessage.value = contenido;
    }
}

function confirmarBloqueo() {
    showBloqueoModal.value = false;
    router.post(`/chat/${props.solicitud.idsolicitud}/bloquear`, { motivo: motivoBloqueoInput.value }, {
        preserveScroll: true, onSuccess: () => { motivoBloqueoInput.value = ''; },
    });
}

function desbloquearEmpresa() {
    router.post(`/chat/${props.solicitud.idsolicitud}/desbloquear`, {}, { preserveScroll: true });
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
    if (typeof window !== 'undefined') window.open(url, '_blank');
}
</script>

<template>
    <div class="p-4 sm:p-6 bg-background min-h-screen text-foreground">

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
                        <p class="font-semibold text-foreground">{{ toastType === 'success' ? 'Acción completada' :
                            'Aviso' }}</p>
                        <p class="mt-1 text-sm text-muted-foreground whitespace-pre-line">{{ toastMessage }}</p>
                    </div>
                    <button type="button" @click="showToast = false"
                        class="text-muted-foreground hover:text-foreground transition-colors">
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>

        <div class="max-w-4xl mx-auto">
            <Card class="overflow-hidden">
                <CardHeader class="border-b border-border bg-gradient-to-r from-primary/5 to-transparent">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <Link href="/chats">
                                <Button variant="ghost" size="icon" class="shrink-0" title="Volver a chats">
                                    <ArrowLeft class="w-5 h-5" />
                                </Button>
                            </Link>
                            <div
                                :class="['flex', 'h-10', 'w-10', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(getOtraEmpresa()).gradient]">
                                <span :class="['text-sm', 'font-bold', getAvatarColor(getOtraEmpresa()).text]">
                                    {{ getOtraEmpresa().charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-foreground leading-tight">{{ getOtraEmpresa() }}</p>
                                <p class="text-xs text-muted-foreground">{{ solicitud.publicacion?.nombre ||
                                    'Intercambio' }}</p>
                            </div>
                        </div>

                        <Dialog v-if="!bloqueadoPorMi" v-model:open="showBloqueoModal">
                            <DialogTrigger as-child>
                                <Button variant="ghost" size="sm" class="text-red-600 hover:text-red-700">
                                    <Ban class="w-4 h-4 mr-1" />
                                    <span class="hidden sm:inline">Bloquear</span>
                                </Button>
                            </DialogTrigger>
                            <DialogContent class="bg-card border-border">
                                <DialogHeader>
                                    <DialogTitle>¿Bloquear empresa?</DialogTitle>
                                    <DialogDescription>
                                        La empresa no podrá enviarte mensajes ni solicitudes. Las solicitudes pendientes
                                        serán canceladas.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="space-y-3 py-4">
                                    <Label for="motivo">Motivo del bloqueo (opcional)</Label>
                                    <Textarea id="motivo" v-model="motivoBloqueoInput"
                                        placeholder="Ej: Comportamiento inapropiado, spam, etc."
                                        class="min-h-[100px]" />
                                    <p class="text-xs text-muted-foreground">El motivo será visible para la empresa
                                        bloqueada.</p>
                                </div>
                                <DialogFooter>
                                    <Button variant="outline" @click="showBloqueoModal = false">Cancelar</Button>
                                    <Button @click="confirmarBloqueo" class="bg-red-600 hover:bg-red-700 text-white">Sí,
                                        bloquear</Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>

                        <Button v-else variant="ghost" size="sm" @click="desbloquearEmpresa"
                            class="text-green-600 hover:text-green-700">
                            <Unlock class="w-4 h-4 mr-1" />
                            <span class="hidden sm:inline">Desbloquear</span>
                        </Button>
                    </div>
                </CardHeader>

                <CardContent class="p-0">
                    <div v-if="bloqueadoHaciaMi"
                        class="m-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 text-sm text-center border border-amber-200 dark:border-amber-800">
                        <Ban class="w-4 h-4 inline mr-1" />
                        Esta empresa te ha bloqueado.
                        <span v-if="motivoBloqueo" class="block mt-1 italic">Motivo: "{{ motivoBloqueo }}"</span>
                    </div>
                    <div v-else-if="bloqueadoPorMi"
                        class="m-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 text-sm text-center border border-blue-200 dark:border-blue-800">
                        Has bloqueado a esta empresa. Desbloquéala para reanudar la conversación.
                    </div>

                    <div ref="mensajesContainer" class="h-[400px] sm:h-[450px] overflow-y-auto p-4 bg-muted/5">
                        <div v-if="mensajes.length === 0"
                            class="flex flex-col items-center justify-center h-full text-muted-foreground">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mb-4">
                                <MessageCircle class="h-8 w-8 text-primary/50" />
                            </div>
                            <p class="text-base font-medium">No hay mensajes aún</p>
                            <p class="text-sm mt-1">¡Envía el primer mensaje!</p>
                        </div>

                        <template v-for="grupo in mensajesAgrupados" :key="grupo.fecha">
                            <div class="flex justify-center my-3">
                                <span class="px-3 py-1 rounded-full bg-muted text-xs text-muted-foreground font-medium">
                                    {{ grupo.fecha }}
                                </span>
                            </div>

                            <div v-for="msg in grupo.mensajes" :key="msg.idmensajes" class="mb-2 flex items-end gap-2"
                                :class="esMio(msg) ? 'justify-end' : 'justify-start'">

                                <template v-if="!esMio(msg)">
                                    <div
                                        :class="['flex', 'h-8', 'w-8', 'shrink-0', 'items-center', 'justify-center', 'rounded-full', 'border', 'bg-gradient-to-br', getAvatarColor(getOtraEmpresa()).gradient, 'mb-1']">
                                        <span :class="['text-xs', 'font-bold', getAvatarColor(getOtraEmpresa()).text]">
                                            {{ (msg.empresa_emisora?.nombreEmpresa || 'E').charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </template>

                                <div class="max-w-[75%]">
                                    <div class="px-3 py-2 rounded-2xl shadow-sm text-sm break-words border border-border"
                                        :class="esMio(msg)
                                            ? 'bg-emerald-500/10 text-foreground dark:bg-emerald-900/30 dark:text-emerald-100 rounded-br-md'
                                            : 'bg-card text-foreground rounded-bl-md'">

                                        <div v-if="msg.tipo === 'imagen'">
                                            <img :src="msg.archivo_url" :alt="msg.archivo_nombre"
                                                class="rounded-lg max-w-full max-h-60 object-cover mb-2 cursor-pointer"
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
                                                <p class="font-medium truncate text-xs">{{ msg.archivo_nombre }}</p>
                                                <p class="text-[10px] text-muted-foreground">
                                                    {{ getExtension(msg.archivo_nombre) }} · {{
                                                    formatSize(msg.archivo_tamano || 0) }}
                                                </p>
                                            </div>
                                            <a :href="getDownloadUrl(msg.archivo_url, msg.archivo_nombre || '')"
                                                target="_blank"
                                                class="text-primary hover:text-primary/80 cursor-pointer">
                                                <Download class="w-4 h-4" />
                                            </a>
                                        </div>

                                        <p v-else>{{ msg.contenido }}</p>
                                    </div>

                                    <div class="flex items-center gap-1 mt-0.5"
                                        :class="esMio(msg) ? 'justify-end' : 'justify-start'">
                                        <span class="text-[10px] text-muted-foreground">
                                            {{ new Date(msg.created_at).toLocaleTimeString([], {
                                                hour: '2-digit',
                                            minute: '2-digit' }) }}
                                        </span>
                                        <CheckCheck v-if="esMio(msg) && msg.leido" class="w-3 h-3 text-blue-500" />
                                        <Check v-else-if="esMio(msg)" class="w-3 h-3 text-muted-foreground" />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div v-if="uploading" class="px-4 pb-2">
                        <div class="h-1 bg-muted rounded-full overflow-hidden">
                            <div class="h-full bg-primary transition-all" :style="{ width: uploadProgress + '%' }">
                            </div>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 text-center">Subiendo... {{ uploadProgress }}%</p>
                    </div>

                    <div class="flex gap-2 items-center p-3 border-t border-border">
                        <input ref="fileInput" type="file" class="hidden" @change="onFileSelected"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z" />
                        <Button variant="ghost" size="icon" @click="fileInput?.click()"
                            :disabled="uploading || noPuedeEnviarMensajes" class="shrink-0 rounded-full"
                            title="Adjuntar archivo">
                            <Paperclip class="w-5 h-5" />
                        </Button>

                        <Input v-model="newMessage" placeholder="Escribe un mensaje..." @keyup.enter="enviarMensaje"
                            :disabled="noPuedeEnviarMensajes" class="flex-1 rounded-full" />

                        <Button @click="enviarMensaje" :disabled="!newMessage.trim() || noPuedeEnviarMensajes"
                            class="bg-primary text-primary-foreground shrink-0 rounded-full" size="icon">
                            <Send class="w-4 h-4" />
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>