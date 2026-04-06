<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';

defineOptions({
    layout: StorefrontLayout,
});

const props = defineProps({
    locations: {
        type: Array,
        default: () => [],
    },
    mapbox: {
        type: Object,
        default: () => ({}),
    },
    stockLocatorUrl: {
        type: String,
        default: null,
    },
    contact: {
        type: Object,
        default: () => ({}),
    },
});

const FALLBACK_MAPBOX_TOKEN = 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

const mapContainer = ref(null);
const search = ref('');
const selectedCategory = ref('all');
const selectedRadius = ref('all');
const selectedStatus = ref('all');
const panelOpen = ref(true);
const selectedStoreId = ref(props.locations[0]?.id ?? null);
const userLocation = ref(null);
const locatingUser = ref(false);
const directions = ref(null);
const directionsError = ref(null);
const mapError = ref(null);

let mapboxgl = null;
let map = null;
let markers = [];
let activePopup = null;
let currentRouteVisible = false;
let userMarker = null;

const allStores = computed(() => props.locations ?? []);

const categories = computed(() => [
    'all',
    ...new Set(allStores.value.flatMap((store) => store.products ?? [])),
]);

const storesWithDistance = computed(() => {
    return allStores.value
        .map((store) => ({
            ...store,
            distanceFromUser: userLocation.value
                ? calculateDistance(
                    userLocation.value.latitude,
                    userLocation.value.longitude,
                    store.latitude,
                    store.longitude,
                )
                : null,
        }))
        .sort((left, right) => {
            if (left.distanceFromUser == null && right.distanceFromUser == null) {
                return left.name.localeCompare(right.name);
            }

            if (left.distanceFromUser == null) {
                return 1;
            }

            if (right.distanceFromUser == null) {
                return -1;
            }

            return left.distanceFromUser - right.distanceFromUser;
        });
});

const filteredStores = computed(() => {
    const term = search.value.trim().toLowerCase();
    const radius = selectedRadius.value === 'all' ? null : Number(selectedRadius.value);

    return storesWithDistance.value.filter((store) => {
        const haystack = [
            store.name,
            store.location,
            store.address,
            ...(store.products ?? []),
            ...(store.features ?? []),
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        const matchesTerm = !term || haystack.includes(term);
        const matchesCategory = selectedCategory.value === 'all' || (store.products ?? []).includes(selectedCategory.value);
        const matchesStatus = selectedStatus.value === 'all' || (store.status ?? 'Open') === selectedStatus.value;
        const matchesRadius = !radius || store.distanceFromUser == null || store.distanceFromUser <= radius;

        return matchesTerm && matchesCategory && matchesStatus && matchesRadius;
    });
});

const selectedStore = computed(() => {
    return storesWithDistance.value.find((store) => store.id === selectedStoreId.value) ?? filteredStores.value[0] ?? null;
});

const nearestStore = computed(() => {
    return storesWithDistance.value.find((store) => store.distanceFromUser != null) ?? null;
});

const shopCount = computed(() => filteredStores.value.length);

const createMarkerElement = (selected = false) => {
    const markerElement = document.createElement('button');
    markerElement.type = 'button';
    markerElement.className = `locator-marker ${selected ? 'is-selected' : ''}`;
    markerElement.innerHTML = '<span>PA</span>';

    return markerElement;
};

const createPopupHtml = (store) => {
    const products = (store.products ?? []).slice(0, 3);

    return `
        <div class="locator-popup-card">
            <h3>${store.name}</h3>
            <p class="locator-popup-address">${store.address}</p>
            ${store.distanceFromUser != null ? `<p class="locator-popup-distance">${store.distanceFromUser.toFixed(1)} km away</p>` : ''}
            <div class="locator-popup-tags">
                ${products.map((product) => `<span>${product}</span>`).join('')}
            </div>
            ${props.stockLocatorUrl ? `<a class="locator-popup-link" href="${props.stockLocatorUrl}">Check Stock Levels</a>` : ''}
        </div>
    `;
};

const clearPopup = () => {
    if (activePopup) {
        activePopup.remove();
        activePopup = null;
    }
};

const syncMarkerSelection = () => {
    markers.forEach(({ marker, storeId }) => {
        marker.getElement().classList.toggle('is-selected', storeId === selectedStoreId.value);
    });
};

const showStorePopup = (store) => {
    if (!map || !mapboxgl) {
        return;
    }

    clearPopup();

    activePopup = new mapboxgl.Popup({
        offset: 20,
        closeButton: true,
    })
        .setLngLat([store.longitude, store.latitude])
        .setHTML(createPopupHtml(store))
        .addTo(map);
};

const rebuildMarkers = () => {
    if (!map || !mapboxgl) {
        return;
    }

    markers.forEach(({ marker }) => marker.remove());
    markers = [];

    filteredStores.value.forEach((store) => {
        const markerElement = createMarkerElement(store.id === selectedStoreId.value);
        markerElement.addEventListener('click', (event) => {
            event.stopPropagation();
            focusStore(store, true);
        });

        const marker = new mapboxgl.Marker(markerElement)
            .setLngLat([store.longitude, store.latitude])
            .addTo(map);

        markers.push({
            storeId: store.id,
            marker,
        });
    });

    syncMarkerSelection();
};

const fitMapToStores = (stores) => {
    if (!map || !mapboxgl || !stores.length) {
        return;
    }

    if (stores.length === 1) {
        map.flyTo({
            center: [stores[0].longitude, stores[0].latitude],
            zoom: 13.5,
            duration: 800,
        });

        return;
    }

    const bounds = new mapboxgl.LngLatBounds();
    stores.forEach((store) => bounds.extend([store.longitude, store.latitude]));

    map.fitBounds(bounds, {
        padding: 80,
        maxZoom: 13,
        duration: 1000,
    });
};

const focusStore = (store, openPopup = false) => {
    if (!store || !map) {
        return;
    }

    selectedStoreId.value = store.id;

    map.flyTo({
        center: [store.longitude, store.latitude],
        zoom: 14,
        duration: 1000,
    });

    if (openPopup) {
        window.setTimeout(() => showStorePopup(store), 250);
    }
};

const togglePanel = () => {
    panelOpen.value = !panelOpen.value;

    if (map) {
        window.setTimeout(() => map.resize(), 250);
    }
};

const searchStores = () => {
    if (!filteredStores.value.length) {
        return;
    }

    focusStore(filteredStores.value[0], true);
    fitMapToStores(filteredStores.value);
};

const locateUser = () => {
    if (!navigator.geolocation || !mapboxgl || !map) {
        return;
    }

    locatingUser.value = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            userLocation.value = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
            };

            locatingUser.value = false;

            if (userMarker) {
                userMarker.remove();
            }

            userMarker = new mapboxgl.Marker({ color: '#111827' })
                .setLngLat([userLocation.value.longitude, userLocation.value.latitude])
                .addTo(map);

            if (nearestStore.value) {
                focusStore(nearestStore.value);
            }
        },
        () => {
            locatingUser.value = false;
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000,
        },
    );
};

const clearDirections = () => {
    directions.value = null;
    directionsError.value = null;

    if (!map || !currentRouteVisible) {
        return;
    }

    if (map.getLayer('route')) {
        map.removeLayer('route');
    }

    if (map.getSource('route')) {
        map.removeSource('route');
    }

    currentRouteVisible = false;
};

const requestDirections = async (store) => {
    directionsError.value = null;

    const origin = userLocation.value
        ? [userLocation.value.longitude, userLocation.value.latitude]
        : selectedStore.value
            ? [selectedStore.value.longitude, selectedStore.value.latitude]
            : null;

    if (!origin || !map || !mapboxgl) {
        directionsError.value = 'Directions are not available right now.';
        return;
    }

    try {
        const response = await fetch(
            `https://api.mapbox.com/directions/v5/mapbox/driving/${origin[0]},${origin[1]};${store.longitude},${store.latitude}?steps=true&geometries=geojson&access_token=${props.mapbox?.token || FALLBACK_MAPBOX_TOKEN}`,
        );
        const payload = await response.json();
        const route = payload.routes?.[0];

        if (!route) {
            directionsError.value = 'Directions could not be loaded for this location.';
            return;
        }

        clearDirections();

        map.addSource('route', {
            type: 'geojson',
            data: {
                type: 'Feature',
                properties: {},
                geometry: route.geometry,
            },
        });

        map.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            layout: {
                'line-join': 'round',
                'line-cap': 'round',
            },
            paint: {
                'line-color': '#ff0000',
                'line-width': 6,
            },
        });

        currentRouteVisible = true;

        const bounds = route.geometry.coordinates.reduce((accumulator, coordinate) => {
            return accumulator.extend(coordinate);
        }, new mapboxgl.LngLatBounds(route.geometry.coordinates[0], route.geometry.coordinates[0]));

        map.fitBounds(bounds, {
            padding: 80,
            duration: 900,
        });

        directions.value = {
            distanceKm: (route.distance / 1000).toFixed(1),
            durationMinutes: Math.round(route.duration / 60),
            steps: route.legs?.[0]?.steps ?? [],
            storeName: store.name,
        };
    } catch (error) {
        directionsError.value = 'Directions could not be loaded for this location.';
    }
};

const initializeMap = () => {
    if (!mapContainer.value || !allStores.value.length || !mapboxgl) {
        return;
    }

    mapboxgl.accessToken = props.mapbox?.token || FALLBACK_MAPBOX_TOKEN;

    map = new mapboxgl.Map({
        container: mapContainer.value,
        style: props.mapbox?.styleLight || 'mapbox://styles/mapbox/streets-v12',
        center: [allStores.value[0].longitude, allStores.value[0].latitude],
        zoom: 12,
    });

    map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');
    map.addControl(new mapboxgl.FullscreenControl(), 'bottom-right');

    map.on('load', () => {
        rebuildMarkers();
        fitMapToStores(filteredStores.value.length ? filteredStores.value : allStores.value);
        locateUser();
    });
};

const formatDistance = (distance) => {
    return distance != null ? `${distance.toFixed(1)} km` : 'N/A';
};

const calculateDistance = (lat1, lng1, lat2, lng2) => {
    const earthRadius = 6371;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLng = ((lng2 - lng1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLng / 2) *
        Math.sin(dLng / 2);

    return earthRadius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
};

watch(filteredStores, (stores) => {
    if (!stores.length) {
        clearPopup();
        return;
    }

    if (!stores.some((store) => store.id === selectedStoreId.value)) {
        selectedStoreId.value = stores[0].id;
    }

    if (map) {
        rebuildMarkers();
        fitMapToStores(stores);
    }
}, { deep: true });

watch(selectedStoreId, (storeId) => {
    if (!map) {
        return;
    }

    syncMarkerSelection();

    const store = filteredStores.value.find((item) => item.id === storeId)
        ?? storesWithDistance.value.find((item) => item.id === storeId);

    if (store) {
        showStorePopup(store);
    }
});

onMounted(async () => {
    try {
        const [mapboxModule] = await Promise.all([
            import('mapbox-gl'),
            import('mapbox-gl/dist/mapbox-gl.css'),
        ]);

        mapboxgl = mapboxModule.default;
        initializeMap();
    } catch (error) {
        mapError.value = 'The Mapbox map could not be loaded.';
    }
});

onBeforeUnmount(() => {
    clearDirections();
    clearPopup();

    if (userMarker) {
        userMarker.remove();
    }

    if (map) {
        map.remove();
        map = null;
    }
});
</script>

<template>
    <Head title="Locations" />

    <section class="space-y-6 py-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <div class="max-w-4xl space-y-4">
                <span class="inline-flex rounded-full bg-brand-500 px-4 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-white">
                    Vending locations
                </span>
                <h1 class="text-4xl font-black tracking-tight text-slate-950 md:text-5xl">
                    Find Pop Attack locations near you.
                </h1>
                <p class="max-w-3xl text-lg leading-8 text-slate-600">
                    This now uses Mapbox again with the same Pop Attack UK token pattern, while keeping the stronger AU-style locations layout and filtering structure.
                </p>
                <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                    <span v-if="stockLocatorUrl">
                        <a :href="stockLocatorUrl" class="font-semibold text-brand-500 hover:underline">
                            Click here to Check Stock Levels
                        </a>
                    </span>
                    <span>Number Of Shops: {{ shopCount }}</span>
                    <span v-if="locatingUser">Loading your location...</span>
                    <span v-else-if="nearestStore">Closest store: {{ nearestStore.name }} ({{ formatDistance(nearestStore.distanceFromUser) }})</span>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_100px_rgba(15,23,42,0.12)]">
        <div class="grid gap-4 lg:grid-cols-[1.5fr_1fr_1fr_1fr_auto]">
            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Search Location</span>
                <input v-model="search" type="text" class="locator-filter-input" placeholder="Suburb, postcode, or venue">
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Category</span>
                <select v-model="selectedCategory" class="locator-filter-input">
                    <option value="all">All Categories</option>
                    <option v-for="category in categories.filter((item) => item !== 'all')" :key="category" :value="category">
                        {{ category }}
                    </option>
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Distance Range</span>
                <select v-model="selectedRadius" class="locator-filter-input">
                    <option value="all">Radius: Km</option>
                    <option value="10">10 km</option>
                    <option value="25">25 km</option>
                    <option value="50">50 km</option>
                    <option value="100">100 km</option>
                </select>
            </label>

            <label class="space-y-2">
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</span>
                <select v-model="selectedStatus" class="locator-filter-input">
                    <option value="all">All</option>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                </select>
            </label>

            <div class="flex items-end gap-3">
                <button type="button" class="primary-button whitespace-nowrap" @click="searchStores">
                    Search
                </button>
                <button type="button" class="secondary-button whitespace-nowrap !border-slate-300 !bg-white !text-slate-800" @click="locateUser">
                    Use Location
                </button>
            </div>
        </div>
    </section>

    <section class="mt-6 relative overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 shadow-[0_24px_100px_rgba(15,23,42,0.16)]">
        <div class="locator-shell">
            <aside class="locator-store-panel" :class="{ 'is-open': panelOpen }">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Store Direction</p>
                        <p class="mt-1 text-lg font-bold text-slate-950">{{ shopCount }} result<span v-if="shopCount !== 1">s</span></p>
                    </div>
                    <button type="button" class="text-xl font-semibold text-slate-400 transition hover:text-slate-950" @click="togglePanel">
                        ×
                    </button>
                </div>

                <div class="locator-store-list">
                    <button
                        v-for="store in filteredStores"
                        :key="store.id"
                        type="button"
                        class="locator-store-card"
                        :class="{ 'is-selected': selectedStoreId === store.id }"
                        @click="focusStore(store, true)"
                    >
                        <div class="space-y-3 text-left">
                            <div>
                                <h3 class="text-lg font-bold text-slate-950">{{ store.name }}</h3>
                                <p class="mt-1 text-sm font-medium text-brand-500">{{ store.location }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ store.address }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-brand-600">
                                    {{ store.status || 'Open' }}
                                </span>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-slate-600">
                                    {{ formatDistance(store.distanceFromUser) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="feature in (store.features ?? []).slice(0, 2)"
                                    :key="feature"
                                    class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-slate-600"
                                >
                                    {{ feature }}
                                </span>
                            </div>
                        </div>
                    </button>

                    <div v-if="!filteredStores.length" class="p-6 text-sm text-slate-500">
                        No matching locations found.
                    </div>
                </div>
            </aside>

            <div class="absolute left-4 top-4 z-[500]">
                <button type="button" class="locator-toggle-button" @click="togglePanel">
                    {{ panelOpen ? 'Hide Stores' : 'Show Stores' }}
                </button>
            </div>

            <div ref="mapContainer" class="locator-map" />

            <div v-if="mapError" class="absolute inset-0 z-[600] flex items-center justify-center bg-white/90 p-6 text-center text-slate-700">
                {{ mapError }}
            </div>
        </div>
    </section>

    <section class="grid gap-6 py-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-500">Description</p>
            <div v-if="selectedStore" class="mt-4 space-y-5">
                <div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-950">{{ selectedStore.name }}</h2>
                    <p class="mt-2 text-base font-semibold text-brand-500">{{ selectedStore.location }}</p>
                    <p class="mt-4 text-base leading-8 text-slate-600">{{ selectedStore.description }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Address</p>
                        <p class="mt-3 text-base leading-7 text-slate-700">{{ selectedStore.address }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Popular Categories</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span
                                v-for="product in selectedStore.products ?? []"
                                :key="product"
                                class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-slate-700 shadow-sm"
                            >
                                {{ product }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <button type="button" class="primary-button" @click="requestDirections(selectedStore)">
                        GET DIRECTIONS
                    </button>
                    <a
                        v-if="stockLocatorUrl"
                        :href="stockLocatorUrl"
                        class="secondary-button !border-slate-300 !bg-white !text-slate-800"
                    >
                        CHECK STOCK LEVELS
                    </a>
                </div>

                <p v-if="directionsError" class="text-sm text-rose-500">{{ directionsError }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-500">Opening Hours</p>
                <div v-if="selectedStore?.hours" class="mt-4 space-y-3">
                    <div
                        v-for="(hours, day) in selectedStore.hours"
                        :key="day"
                        class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm"
                    >
                        <span class="font-semibold capitalize text-slate-950">{{ day }}</span>
                        <span class="text-slate-600">{{ hours }}</span>
                    </div>
                </div>
            </div>

            <div v-if="directions" class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-500">GET DIRECTIONS</p>
                        <h3 class="mt-2 text-2xl font-black tracking-tight text-slate-950">{{ directions.storeName }}</h3>
                        <p class="mt-3 text-sm text-slate-500">
                            {{ directions.distanceKm }} Km · {{ directions.durationMinutes }} minutes
                        </p>
                    </div>
                    <button type="button" class="text-2xl leading-none text-slate-400 transition hover:text-slate-950" @click="clearDirections">
                        ×
                    </button>
                </div>

                <div class="mt-6 space-y-3">
                    <div
                        v-for="(step, index) in directions.steps"
                        :key="`${step.maneuver?.instruction}-${index}`"
                        class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white">
                            {{ index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-950">{{ step.maneuver?.instruction }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-500">{{ (step.distance / 1000).toFixed(1) }} Km</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_80px_rgba(15,23,42,0.12)]">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-500">Use my location to find the closest service provider near me</p>
                <p class="mt-3 text-base leading-8 text-slate-600">
                    This mirrors the intent of the live AU locator utility while using the Pop Attack UK Mapbox approach underneath for the map, directions, and marker interactions.
                </p>
                <div class="mt-5 space-y-2 text-sm text-slate-600">
                    <p v-if="contact.phone"><strong class="text-slate-950">Phone:</strong> {{ contact.phone }}</p>
                    <p v-if="contact.email"><strong class="text-slate-950">Email:</strong> {{ contact.email }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.locator-filter-input {
    width: 100%;
    border-radius: 1rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    padding: 0.95rem 1rem;
    color: #0f172a;
    outline: none;
}

.locator-filter-input:focus {
    border-color: #ff0000;
}

.locator-shell {
    position: relative;
    min-height: 42rem;
}

.locator-map {
    height: 42rem;
    width: 100%;
}

.locator-store-panel {
    position: absolute;
    left: 1rem;
    top: 4.75rem;
    bottom: 1rem;
    z-index: 500;
    width: min(28rem, calc(100% - 2rem));
    overflow: hidden;
    border-radius: 1.75rem;
    border: 1px solid rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.18);
    backdrop-filter: blur(14px);
    transform: translateX(calc(-100% - 2rem));
    transition: transform 0.25s ease;
}

.locator-store-panel.is-open {
    transform: translateX(0);
}

.locator-store-list {
    height: calc(100% - 5.25rem);
    overflow-y: auto;
    padding: 1rem;
}

.locator-store-card {
    width: 100%;
    border-radius: 1.25rem;
    border: 1px solid #e2e8f0;
    background: white;
    padding: 1rem;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}

.locator-store-card + .locator-store-card {
    margin-top: 0.75rem;
}

.locator-store-card:hover,
.locator-store-card.is-selected {
    border-color: rgba(255, 0, 0, 0.35);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
}

.locator-toggle-button {
    border-radius: 9999px;
    background: #ff0000;
    color: white;
    padding: 0.75rem 1rem;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
}

:deep(.mapboxgl-canvas),
:deep(.mapboxgl-map) {
    height: 100%;
    width: 100%;
}

:deep(.locator-marker) {
    display: flex;
    height: 3.25rem;
    width: 3.25rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    border: 4px solid #fff;
    background: #ff0000;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.2);
    color: white;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

:deep(.locator-marker.is-selected) {
    background: #111827;
    transform: scale(1.06);
}

:deep(.mapboxgl-popup-content) {
    border-radius: 1.25rem;
    padding: 0;
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.18);
}

:deep(.locator-popup-card) {
    padding: 1.2rem;
}

:deep(.locator-popup-card h3) {
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 800;
}

:deep(.locator-popup-address) {
    margin-top: 0.45rem;
    color: #64748b;
    font-size: 0.92rem;
    line-height: 1.6;
}

:deep(.locator-popup-distance) {
    margin-top: 0.7rem;
    color: #ff0000;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

:deep(.locator-popup-tags) {
    margin-top: 0.9rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

:deep(.locator-popup-tags span) {
    border-radius: 9999px;
    background: #f8fafc;
    padding: 0.45rem 0.7rem;
    color: #475569;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

:deep(.locator-popup-link) {
    margin-top: 1rem;
    display: inline-flex;
    border-radius: 9999px;
    background: #ff0000;
    padding: 0.8rem 1rem;
    color: white;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-decoration: none;
    text-transform: uppercase;
}

@media (max-width: 960px) {
    .locator-store-panel {
        right: 1rem;
        width: auto;
    }
}

@media (max-width: 768px) {
    .locator-map {
        height: 36rem;
    }

    .locator-store-panel {
        left: 0.75rem;
        right: 0.75rem;
        bottom: 0.75rem;
        width: auto;
    }
}
</style>
