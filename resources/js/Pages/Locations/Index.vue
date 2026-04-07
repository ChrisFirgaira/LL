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

const FALLBACK_MAPBOX_TOKEN = 'pk.eyJ1IjoidmVuZGFibGVzIiwiYSI6ImNtZHNycWcxazBoZm4ya29ubnR6ZGY4NWkifQ.db2JuRNjd7tcV1YLTjAmIg';

const sectionRef = ref(null);
const mapContainer = ref(null);
const panelOpen = ref(false);
const selectedStoreId = ref(props.locations[0]?.id ?? null);
const userLocation = ref(null);
const locatingUser = ref(false);
const directions = ref(null);
const directionsError = ref(null);
const mapError = ref(null);
const mapViewportHeight = ref('100vh');

let mapboxgl = null;
let map = null;
let markers = [];
let activePopup = null;
let currentRouteVisible = false;
let userMarker = null;
let mapResizeObserver = null;
let viewportResizeHandler = null;
let previousBodyOverflow = null;
let previousBodyOverscroll = null;

const allStores = computed(() => props.locations ?? []);

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

const filteredStores = computed(() => storesWithDistance.value);

const selectedStore = computed(() => {
    return storesWithDistance.value.find((store) => store.id === selectedStoreId.value) ?? filteredStores.value[0] ?? null;
});

const nearestStore = computed(() => {
    return storesWithDistance.value.find((store) => store.distanceFromUser != null) ?? null;
});

const shopCount = computed(() => filteredStores.value.length);
const panelToggleLabel = computed(() => {
    const nearestDistance = nearestStore.value?.distanceFromUser;
    const distanceLabel = nearestDistance != null ? ` - ${nearestDistance.toFixed(1)} km from me` : '';

    return panelOpen.value ? `Hide Locations${distanceLabel}` : `Show Locations${distanceLabel}`;
});

const createMarkerElement = (selected = false) => {
    const markerElement = document.createElement('button');
    markerElement.type = 'button';
    markerElement.className = `locator-marker ${selected ? 'is-selected' : ''}`;
    markerElement.innerHTML = `
        <span class="locator-marker-pin">
            <img src="/images/popattack-logo.png" alt="Pop Attack">
        </span>
    `;

    return markerElement;
};

const createPopupHtml = (store) => {
    const mapsUrl = userLocation.value
        ? `https://www.google.com/maps/dir/${userLocation.value.latitude},${userLocation.value.longitude}/${store.latitude},${store.longitude}`
        : `https://www.google.com/maps/search/?api=1&query=${store.latitude},${store.longitude}`;

    return `
        <div class="locator-popup-card">
            <h3 class="locator-popup-title">${store.name}</h3>
            <p class="locator-popup-address">${store.address}</p>
            ${store.distanceFromUser != null ? `<p class="locator-popup-distance"><span class="locator-popup-distance-pin">📍</span> ${store.distanceFromUser.toFixed(1)}km from you</p>` : ''}
            <div class="locator-popup-actions">
                ${props.stockLocatorUrl ? `<a class="locator-popup-link is-primary" href="${props.stockLocatorUrl}">Check Stock</a>` : ''}
                <a class="locator-popup-link is-secondary" href="${mapsUrl}" target="_blank" rel="noopener noreferrer">Get Directions</a>
            </div>
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
        maxWidth: '360px',
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

const updateMapViewportHeight = () => {
    if (!sectionRef.value || typeof window === 'undefined') {
        return;
    }

    const topOffset = Math.max(sectionRef.value.getBoundingClientRect().top, 0);
    const availableHeight = Math.max(window.innerHeight - topOffset, 320);
    mapViewportHeight.value = `${availableHeight}px`;
    map?.resize();
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

    if (!map || !mapboxgl) {
        const destination = `${store.latitude},${store.longitude}`;
        const googleMapsUrl = origin
            ? `https://www.google.com/maps/dir/${origin[1]},${origin[0]}/${destination}`
            : `https://www.google.com/maps/search/?api=1&query=${destination}`;
        window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');
        return;
    }

    if (!origin) {
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

    const baseMapStyle = {
        version: 8,
        sources: {
            osm: {
                type: 'raster',
                tiles: [
                    'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    'https://b.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    'https://c.tile.openstreetmap.org/{z}/{x}/{y}.png',
                ],
                tileSize: 256,
                attribution: '&copy; OpenStreetMap contributors',
            },
        },
        layers: [
            {
                id: 'osm-base',
                type: 'raster',
                source: 'osm',
            },
        ],
    };

    try {
        map = new mapboxgl.Map({
            container: mapContainer.value,
            style: props.mapbox?.styleLight || 'mapbox://styles/mapbox/streets-v12',
            center: [allStores.value[0].longitude, allStores.value[0].latitude],
            zoom: 12,
        });
    } catch (error) {
        mapError.value = 'Desktop WebGL map could not initialize. Showing fallback map.';
        return;
    }

    map.addControl(new mapboxgl.NavigationControl({ showCompass: true, showZoom: true }), 'top-right');
    map.addControl(new mapboxgl.FullscreenControl(), 'top-right');
    map.addControl(new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true,
        },
        trackUserLocation: false,
        showUserHeading: false,
    }), 'top-right');

    map.on('load', () => {
        mapError.value = null;

        // Hide built-in Mapbox symbol overlays (labels/icons) like on UK implementation.
        const styleLayers = map.getStyle()?.layers ?? [];
        styleLayers
            .filter((layer) => layer.type === 'symbol')
            .forEach((layer) => {
                if (layer.id) {
                    map.setLayoutProperty(layer.id, 'visibility', 'none');
                }
            });

        rebuildMarkers();
        fitMapToStores(filteredStores.value.length ? filteredStores.value : allStores.value);
        locateUser();
        window.setTimeout(() => map?.resize(), 100);
        window.setTimeout(() => map?.resize(), 350);
        window.setTimeout(() => map?.resize(), 800);
    });

    map.on('error', (event) => {
        const statusCode = event?.error?.status || event?.error?.statusCode;
        if ((statusCode === 401 || statusCode === 403) && map?.getStyle()?.sprite) {
            map.setStyle(baseMapStyle);
        }
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
        if (typeof document !== 'undefined') {
            previousBodyOverflow = document.body.style.overflow;
            previousBodyOverscroll = document.body.style.overscrollBehaviorY;
            document.body.style.overflow = 'hidden';
            document.body.style.overscrollBehaviorY = 'none';
        }

        updateMapViewportHeight();
        viewportResizeHandler = () => updateMapViewportHeight();
        window.addEventListener('resize', viewportResizeHandler, { passive: true });

        const [mapboxModule] = await Promise.all([
            import('mapbox-gl'),
            import('mapbox-gl/dist/mapbox-gl.css'),
        ]);

        mapboxgl = mapboxModule.default;
        initializeMap();

        if (window.ResizeObserver && mapContainer.value) {
            mapResizeObserver = new window.ResizeObserver(() => {
                updateMapViewportHeight();
                map?.resize();
            });
            mapResizeObserver.observe(mapContainer.value);
        }
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

    if (mapResizeObserver) {
        mapResizeObserver.disconnect();
        mapResizeObserver = null;
    }

    if (viewportResizeHandler) {
        window.removeEventListener('resize', viewportResizeHandler);
        viewportResizeHandler = null;
    }

    if (typeof document !== 'undefined') {
        document.body.style.overflow = previousBodyOverflow ?? '';
        document.body.style.overscrollBehaviorY = previousBodyOverscroll ?? '';
    }
});
</script>

<template>
    <Head title="Locations" />

    <section ref="sectionRef" class="w-full bg-slate-100">
        <div class="locator-shell" :style="{ height: mapViewportHeight, minHeight: mapViewportHeight }">
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

            <div class="absolute left-4 top-4 z-[500] flex items-center gap-3">
                <button type="button" class="locator-toggle-button" @click="togglePanel">
                    {{ panelToggleLabel }}
                </button>
                <button type="button" class="secondary-button whitespace-nowrap !border-slate-300 !bg-white !text-slate-800" @click="locateUser">
                    {{ locatingUser ? 'Locating...' : 'Use Location' }}
                </button>
            </div>

            <div ref="mapContainer" class="locator-map" />

            <div v-if="mapError" class="absolute inset-0 z-[600] bg-white/95 p-6 text-slate-700">
                <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 text-sm">
                    {{ mapError }}
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.locator-shell {
    position: relative;
    height: 100dvh;
    min-height: 100dvh;
}

.locator-map {
    height: 100%;
    width: 100%;
}

.locator-map.mapboxgl-map {
    height: 100% !important;
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

:deep(.mapboxgl-canvas) {
    height: 100%;
    width: 100%;
    filter: none !important;
    opacity: 1 !important;
}

:deep(.locator-marker) {
    display: flex;
    height: 3.9rem;
    width: 3.9rem;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
    background: transparent;
    border: 0;
}

:deep(.locator-marker.is-selected) {
    transform: scale(1.05);
}

:deep(.locator-marker-pin) {
    position: relative;
    display: flex;
    height: 3.05rem;
    width: 3.05rem;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    border-radius: 50% 50% 50% 0;
    background: #ff0000;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.2);
    transform: rotate(-45deg);
}

:deep(.locator-marker.is-selected .locator-marker-pin) {
    background: #ff0000;
}

:deep(.locator-marker-pin img) {
    height: 1.75rem;
    width: 1.75rem;
    border-radius: 9999px;
    object-fit: cover;
    background: #fff;
    transform: rotate(45deg);
}

:deep(.mapboxgl-ctrl-logo),
:deep(.mapboxgl-ctrl-attrib) {
    display: none !important;
}

:deep(.mapboxgl-popup-content) {
    border-radius: 1.5rem;
    padding: 0;
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.18);
    width: min(22rem, 86vw);
    max-width: min(22rem, 86vw);
    box-sizing: border-box;
}

:deep(.locator-popup-card) {
    width: 100%;
    padding: 1.3rem 1.15rem 1.15rem;
    text-align: center;
    box-sizing: border-box;
}

:deep(.locator-popup-title) {
    color: #0f172a;
    font-size: 1.08rem;
    line-height: 1.12;
    font-weight: 800;
    letter-spacing: -0.02em;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}

:deep(.locator-popup-address) {
    margin-top: 0.65rem;
    color: #64748b;
    font-size: 0.8rem;
    line-height: 1.5;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}

:deep(.locator-popup-distance) {
    margin: 0.85rem auto 0;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid #fca5a5;
    border-radius: 0.75rem;
    background: #fee2e2;
    padding: 0.55rem 0.9rem;
    color: #ff0000;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}

:deep(.locator-popup-distance-pin) {
    font-size: 0.72rem;
}

:deep(.locator-popup-link) {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.6rem;
    padding: 0.74rem 0.95rem;
    color: white;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.01em;
    text-decoration: none;
}

:deep(.locator-popup-actions) {
    margin-top: 1.2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.6rem;
}

:deep(.locator-popup-link.is-primary) {
    background: #ff0000;
    box-shadow: 0 10px 22px rgba(220, 38, 38, 0.35);
}

:deep(.locator-popup-link.is-secondary) {
    border: 1px solid #d1d5db;
    background: #f3f4f6;
    color: #4b5563;
}

:deep(.mapboxgl-popup-close-button) {
    top: -0.8rem;
    right: calc(50% - 0.8rem);
    height: 1.7rem;
    width: 1.7rem;
    border: 3px solid #fff;
    border-radius: 9999px;
    background: #ff0000;
    color: #fff;
    font-size: 1rem;
    font-weight: 800;
    line-height: 1;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.2);
}

:deep(.mapboxgl-popup-close-button:hover) {
    background: #e11d48;
}

@media (max-width: 960px) {
    .locator-shell {
        height: 100dvh;
        min-height: 100dvh;
    }

    .locator-store-panel {
        right: 1rem;
        width: auto;
        top: 4.75rem;
    }

    .locator-toggle-button {
        font-size: 0.72rem;
        padding: 0.62rem 0.85rem;
    }
}

@media (max-width: 768px) {
    .locator-shell {
        height: 100dvh;
        min-height: 100dvh;
    }

    .locator-store-panel {
        left: 0.75rem;
        right: 0.75rem;
        bottom: 0.75rem;
        top: 4.75rem;
        width: auto;
    }
}
</style>
