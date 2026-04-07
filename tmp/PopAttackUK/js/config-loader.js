// Configuration loader - fetches config from server
class ConfigLoader {
    static async loadConfig() {
        try {
            // In production, fetch from your server endpoint
            const response = await fetch('/api/config');
            if (response.ok) {
                const config = await response.json();
                window.MAPBOX_TOKEN = config.mapboxToken;
                window.APP_CONFIG = config;
                return config;
            }
        } catch (error) {
            console.warn('Failed to load server config, using defaults');
        }
        
        // Fallback for development
        return {
            mapboxToken: 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
            mapStyle: 'mapbox://styles/mapbox/streets-v12'
        };
    }
    
    static async loadStoreData() {
        try {
            // In production, fetch from your server endpoint
            const response = await fetch('/api/stores');
            if (response.ok) {
                const storeData = await response.json();
                window.storeData = storeData;
                return storeData;
            }
        } catch (error) {
            console.warn('Failed to load server store data, using local data');
        }
        
        // Fallback to local data
        return window.storeData;
    }
}

window.ConfigLoader = ConfigLoader;