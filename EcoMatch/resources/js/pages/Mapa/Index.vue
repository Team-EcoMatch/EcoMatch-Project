<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { ref, computed, watch } from 'vue';
import { Search, MapPin, X, Building2, Crosshair } from 'lucide-vue-next';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

const customIcon: any = L.divIcon({
    html: '<div style="background-color: #10B981; width: 24px; height: 24px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>',
    className: '',
    iconSize: [24, 24],
    iconAnchor: [12, 12]
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

const center: [number, number] = [4.7110, -74.0721]; 
const zoom = 6;

const mapRef = ref<any>(null);
const searchQuery = ref('');
const geoResults = ref<SearchResult[]>([]);
const userLocation = ref<{ lat: number; lng: number } | null>(null);
let searchTimeout: any = null;

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
</script>

<template>
    <div class="p-6 bg-background min-h-screen text-foreground">
        <Head title="Mapa Interactivo" />
        
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight">Mapa de Materiales Disponibles</h2>
                    <p class="text-muted-foreground text-sm mt-1">Haz clic en el mapa para marcar tu ubicación y ver la distancia a las empresas.</p>
                </div>
                <button v-if="userLocation" @click="clearUserLocation" class="flex items-center gap-2 px-4 py-2 bg-destructive/10 text-destructive border border-destructive/20 rounded-full text-sm font-medium hover:bg-destructive/20 transition-colors">
                    <X class="w-4 h-4" />
                    Quitar mi ubicación
                </button>
            </div>

            <div class="relative w-full h-[600px] rounded-xl border border-border overflow-hidden z-0">
                
                <div class="absolute top-5 left-1/2 -translate-x-1/2 z-[1000] w-96 max-w-[90%]">
                    <div class="relative">
                        <div class="flex items-center w-full bg-background/95 backdrop-blur-lg border border-border/60 rounded-full shadow-2xl pl-5 pr-2 py-2 focus-within:ring-2 focus-within:ring-primary transition-all">
                            <Search class="w-5 h-5 text-muted-foreground shrink-0" />
                            <input 
                                v-model="searchQuery" 
                                type="text" 
                                placeholder="Buscar empresa, ciudad o dirección..."
                                class="w-full pl-3 pr-2 bg-transparent text-foreground text-sm focus:outline-none placeholder:text-muted-foreground/80"
                                @keydown.enter="handleEnter"
                            />
                            <button v-if="searchQuery" @click="searchQuery = ''" class="p-1.5 rounded-full hover:bg-muted transition-colors">
                                <X class="w-4 h-4 text-muted-foreground" />
                            </button>
                        </div>
                        
                        <div v-if="searchQuery" class="absolute top-14 left-0 right-0 bg-background/95 backdrop-blur-lg border border-border/60 rounded-3xl shadow-2xl overflow-hidden max-h-72 overflow-y-auto">
                            <button 
                                v-if="combinedResults.length > 0"
                                v-for="result in combinedResults" 
                                :key="result.id" 
                                @click="flyTo(result)"
                                class="w-full flex items-center gap-4 px-5 py-3 text-left text-sm hover:bg-muted transition-colors border-b border-border/40 last:border-b-0"
                            >
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" :class="result.type === 'empresa' ? 'bg-primary/15' : 'bg-blue-500/15'">
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

                <LMap 
                    ref="mapRef"
                    :zoom="zoom" 
                    :center="center" 
                    class="w-full h-full"
                    @click="onMapClick"
                >
                    <LTileLayer
                        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                        attribution="&copy; OpenStreetMap contributors"
                    />
                    
                    <LMarker 
                        v-if="userLocation"
                        :lat-lng="[userLocation.lat, userLocation.lng]"
                        :icon="userIcon"
                    >
                        <LPopup>
                            <div class="p-2 text-center">
                                <p class="font-semibold text-sm text-blue-600 flex items-center justify-center gap-1">
                                    <Crosshair class="w-4 h-4" />
                                    Tu ubicación
                                </p>
                            </div>
                        </LPopup>
                    </LMarker>

                    <LMarker 
                        v-for="emp in empresas" 
                        :key="emp.idempresa"
                        :lat-lng="[Number(emp.latitud), Number(emp.longitud)]"
                        :icon="customIcon"
                    >
                        <LPopup>
                            <div class="p-3 w-48 font-sans text-center">
                                <h3 class="text-base font-bold tracking-tight text-gray-900 border-b border-gray-100 pb-2 mb-2">
                                    {{ emp.nombreEmpresa }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-2">
                                    <span class="text-xl font-extrabold text-emerald-600">{{ emp.publicaciones_count }}</span> 
                                    <br>materiales disponibles
                                </p>

                                <div v-if="userLocation" class="my-2 py-2 border-y border-gray-100">
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Distancia desde tu ubicación</p>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ calculateDistance(userLocation.lat, userLocation.lng, Number(emp.latitud), Number(emp.longitud)) }}
                                    </p>
                                </div>

                                <a :href="`/publicaciones`" 
                                    style="color: white !important"
                                   class="mt-2 inline-flex items-center justify-center w-full px-3 py-2 text-xs font-semibold text-black bg-emerald-600 rounded-md hover:bg-emerald-700 transition-colors shadow-sm">
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