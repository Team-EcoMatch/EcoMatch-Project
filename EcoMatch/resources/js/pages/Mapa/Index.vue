<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Search, MapPin, X, Building2, Crosshair, PackageCheck, Layers } from 'lucide-vue-next';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const customIcon: any = L.divIcon({
    html: '<div style="background-color: #10B981; width: 28px; height: 28px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;"><div style="background: white; width: 8px; height: 8px; border-radius: 50%;"></div></div>',
    className: '',
    iconSize: [28, 28],
    iconAnchor: [14, 14]
});

const myCompanyIcon: any = L.divIcon({
    html: '<div style="background-color: #6366F1; width: 28px; height: 28px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;"><div style="background: white; width: 8px; height: 8px; border-radius: 50%;"></div></div>',
    className: '',
    iconSize: [28, 28],
    iconAnchor: [14, 14]
});

const userIcon: any = L.divIcon({
    html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>',
    className: '',
    iconSize: [20, 20],
    iconAnchor: [10, 10]
});

interface Empresa {
    idempresa: number;
    nombreEmpresa: string;
    direccion: string;
    latitud: number | null;
    longitud: number | null;
    publicaciones_count: number;
}

interface SearchResult {
    id: string | number;
    type: 'empresa' | 'lugar';
    nombre: string;
    subtitulo: string;
    lat: number;
    lng: number;
    zoom: number;
}

const props = defineProps<{
    empresas: Empresa[];
}>();

const page = usePage();
const miEmpresaId = page.props.auth?.user?.idempresa;

const center: [number, number] = [4.7110, -74.0721];
const zoom = 6;

const mapRef = ref<any>(null);
const searchQuery = ref('');
const geoResults = ref<SearchResult[]>([]);
const userLocation = ref<{ lat: number; lng: number } | null>(null);
let searchTimeout: any = null;

const stats = computed(() => ({
    empresas: props.empresas.length,
    materiales: props.empresas.reduce((sum, e) => sum + e.publicaciones_count, 0),
}));

const localResults = computed<SearchResult[]>(() => {
    if (!searchQuery.value) return [];
    const query = searchQuery.value.toLowerCase();
    return props.empresas
        .filter(emp =>
            emp.nombreEmpresa.toLowerCase().includes(query) ||
            emp.direccion.toLowerCase().includes(query)
        )
        .map(emp => ({
            id: `emp-${emp.idempresa}`,
            type: 'empresa',
            nombre: emp.nombreEmpresa,
            subtitulo: emp.direccion,
            lat: Number(emp.latitud),
            lng: Number(emp.longitud),
            zoom: 14
        }));
});

const combinedResults = computed<SearchResult[]>(() => {
    return [...localResults.value, ...geoResults.value];
});

watch(searchQuery, (newQuery) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    if (!newQuery) {
        geoResults.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(newQuery)}&limit=4`);
            const data = await res.json();
            geoResults.value = data.map((item: any) => ({
                id: `geo-${item.place_id}`,
                type: 'lugar',
                nombre: item.display_name.split(',')[0],
                subtitulo: item.display_name,
                lat: parseFloat(item.lat),
                lng: parseFloat(item.lon),
                zoom: 12
            }));
        } catch (error) {
            geoResults.value = [];
        }
    }, 600);
});

function flyTo(result: SearchResult) {
    if (mapRef.value && mapRef.value.leafletObject) {
        const map = mapRef.value.leafletObject;
        map.flyTo([result.lat, result.lng], result.zoom, { duration: 1.5 });

        searchQuery.value = '';

        if (result.type === 'empresa') {
            setTimeout(() => {
                const marker = map._layers;
                for (let key in marker) {
                    if (marker[key].getLatLng && marker[key].getLatLng().lat == result.lat) {
                        marker[key].openPopup();
                    }
                }
            }, 1600);
        }
    }
}

function handleEnter() {
    if (combinedResults.value.length > 0) {
        flyTo(combinedResults.value[0]);
    }
}

function onMapClick(e: any) {
    userLocation.value = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
    };
}

function calculateDistance(lat1: number, lng1: number, lat2: number, lng2: number): string {
    const from = L.latLng(lat1, lng1);
    const to = L.latLng(lat2, lng2);
    const distanceMeters = from.distanceTo(to);

    if (distanceMeters > 1000) {
        return (distanceMeters / 1000).toFixed(2) + ' km';
    }
    return Math.round(distanceMeters) + ' metros';
}

function clearUserLocation() {
    userLocation.value = null;
}

function getIconForEmpresa(emp: Empresa) {
    return emp.idempresa === miEmpresaId ? myCompanyIcon : customIcon;
}
</script>

<template>
    <div class="p-4 sm:p-6 bg-background min-h-screen text-foreground">

        <Head title="Mapa Interactivo" />

        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <MapPin class="w-6 h-6 text-primary" />
                        Mapa de Materiales
                    </h2>
                    <p class="text-sm text-muted-foreground mt-1">
                        Haz clic en el mapa para marcar tu ubicación y ver la distancia a las empresas.
                    </p>
                </div>
                <button v-if="userLocation" @click="clearUserLocation"
                    class="flex items-center gap-2 px-4 py-2 bg-red-500/10 text-red-500 border border-red-500/20 rounded-full text-sm font-medium hover:bg-red-500/20 transition-colors shrink-0">
                    <X class="w-4 h-4" />
                    Quitar mi ubicación
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 shrink-0">
                        <Building2 class="h-5 w-5 text-emerald-500" />
                    </div>
                    <div>
                        <p class="text-xl font-bold tabular-nums">{{ stats.empresas }}</p>
                        <p class="text-xs text-muted-foreground">Empresas en el mapa</p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-transparent">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 shrink-0">
                        <PackageCheck class="h-5 w-5 text-blue-500" />
                    </div>
                    <div>
                        <p class="text-xl font-bold tabular-nums">{{ stats.materiales }}</p>
                        <p class="text-xs text-muted-foreground">Materiales disponibles</p>
                    </div>
                </div>
            </div>

            <div
                class="relative w-full h-[500px] sm:h-[600px] rounded-2xl border border-border overflow-hidden z-0 shadow-lg">

                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[1000] w-96 max-w-[90%]">
                    <div class="relative">
                        <div
                            class="flex items-center w-full bg-background/95 backdrop-blur-lg border border-border/60 rounded-full shadow-2xl pl-5 pr-2 py-2.5 focus-within:ring-2 focus-within:ring-primary transition-all">
                            <Search class="w-5 h-5 text-muted-foreground shrink-0" />
                            <input v-model="searchQuery" type="text" placeholder="Buscar empresa, ciudad o dirección..."
                                class="w-full pl-3 pr-2 bg-transparent text-foreground text-sm focus:outline-none placeholder:text-muted-foreground/80"
                                @keydown.enter="handleEnter" />
                            <button v-if="searchQuery" @click="searchQuery = ''"
                                class="p-1.5 rounded-full hover:bg-muted transition-colors">
                                <X class="w-4 h-4 text-muted-foreground" />
                            </button>
                        </div>

                        <div v-if="searchQuery"
                            class="absolute top-14 left-0 right-0 bg-background/95 backdrop-blur-lg border border-border/60 rounded-2xl shadow-2xl overflow-hidden max-h-72 overflow-y-auto">
                            <button v-if="combinedResults.length > 0" v-for="result in combinedResults" :key="result.id"
                                @click="flyTo(result)"
                                class="w-full flex items-center gap-4 px-5 py-3 text-left text-sm hover:bg-muted transition-colors border-b border-border/40 last:border-b-0">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                    :class="result.type === 'empresa' ? 'bg-primary/15' : 'bg-blue-500/15'">
                                    <Building2 v-if="result.type === 'empresa'" class="w-5 h-5 text-primary" />
                                    <MapPin v-else class="w-5 h-5 text-blue-500" />
                                </div>
                                <div class="flex flex-col overflow-hidden">
                                    <span class="font-semibold text-foreground truncate">{{ result.nombre }}</span>
                                    <span class="text-xs text-muted-foreground truncate">{{ result.subtitulo }}</span>
                                </div>
                            </button>

                            <div v-else class="px-5 py-8 text-center text-sm text-muted-foreground">
                                No se encontraron resultados.
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute bottom-4 left-4 z-[500] bg-background/90 backdrop-blur-md border border-border/60 rounded-xl shadow-lg p-3 space-y-2">
                    <p class="text-xs font-semibold text-foreground flex items-center gap-1.5 mb-1">
                        <Layers class="w-3.5 h-3.5" />
                        Leyenda
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow"></div>
                        <span class="text-xs text-muted-foreground">Otras empresas</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-indigo-500 border-2 border-white shadow"></div>
                        <span class="text-xs text-muted-foreground">Mi empresa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500 border-2 border-white shadow"></div>
                        <span class="text-xs text-muted-foreground">Mi ubicación</span>
                    </div>
                </div>

                <LMap ref="mapRef" :zoom="zoom" :center="center" class="w-full h-full" @click="onMapClick">
                    <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        attribution="&copy; OpenStreetMap contributors" />

                    <LMarker v-if="userLocation" :lat-lng="[userLocation.lat, userLocation.lng]" :icon="userIcon">
                        <LPopup>
                            <div class="p-2 text-center">
                                <p class="font-semibold text-sm text-blue-600 flex items-center justify-center gap-1">
                                    <Crosshair class="w-4 h-4" />
                                    Tu ubicación
                                </p>
                            </div>
                        </LPopup>
                    </LMarker>

                    <LMarker v-for="emp in empresas" :key="emp.idempresa"
                        :lat-lng="[Number(emp.latitud), Number(emp.longitud)]" :icon="getIconForEmpresa(emp)">
                        <LPopup>
                            <div class="p-4 w-52 text-center">
                                <h3 class="text-base font-bold tracking-tight border-b border-gray-200 pb-2 mb-2"
                                    :class="emp.idempresa === miEmpresaId ? 'text-indigo-600' : 'text-gray-900'">
                                    {{ emp.nombreEmpresa }}
                                </h3>

                                <div class="mb-3">
                                    <p class="text-2xl font-extrabold text-emerald-600">{{ emp.publicaciones_count }}
                                    </p>
                                    <p class="text-xs text-gray-500">materiales disponibles</p>
                                </div>

                                <div v-if="userLocation"
                                    class="mb-3 py-2 px-3 rounded-lg bg-gray-50 border border-gray-100">
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Distancia</p>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ calculateDistance(userLocation.lat, userLocation.lng, Number(emp.latitud),
                                            Number(emp.longitud)) }}
                                    </p>
                                </div>

                                <a v-if="emp.idempresa === miEmpresaId" :href="`/publicaciones`"
                                    style="color: white !important"
                                    class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                    <Building2 class="w-3.5 h-3.5 mr-1" />
                                    Ver Mis Materiales
                                </a>

                                <a v-else :href="`/buscar?lat=${emp.latitud}&lng=${emp.longitud}&radio=50`"
                                    style="color: white !important"
                                    class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                    <PackageCheck class="w-3.5 h-3.5 mr-1" />
                                    Ver Materiales
                                </a>
                            </div>
                        </LPopup>
                    </LMarker>
                </LMap>
            </div>
        </div>
    </div>
</template>