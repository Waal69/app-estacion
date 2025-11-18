<div class="map-container">
    <div class="map-header">
        <h2>Mapa de Clientes</h2>
        <a href="/app-estacion/administrator" class="btn-secondary">Volver</a>
    </div>
    
    <div id="map"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Inicializar mapa
const map = L.map('map').setView([-34.6118, -58.3960], 2);

// Agregar capa de mapa
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Cargar datos de clientes
async function cargarClientesEnMapa() {
    try {
        const response = await fetch('/app-estacion/api/clients-location?list-clients-location=1');
        const clientes = await response.json();
        
        clientes.forEach(cliente => {
            const lat = parseFloat(cliente.latitud);
            const lng = parseFloat(cliente.longitud);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng]).addTo(map);
                
                marker.bindPopup(`
                    <div class="popup-content">
                        <strong>IP:</strong> ${cliente.ip}<br>
                        <strong>Accesos:</strong> ${cliente.accesos}
                    </div>
                `);
            }
        });
        
        // Ajustar vista si hay marcadores
        if (clientes.length > 0) {
            const group = new L.featureGroup();
            clientes.forEach(cliente => {
                const lat = parseFloat(cliente.latitud);
                const lng = parseFloat(cliente.longitud);
                if (!isNaN(lat) && !isNaN(lng)) {
                    group.addLayer(L.marker([lat, lng]));
                }
            });
            
            if (group.getLayers().length > 0) {
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
        
    } catch (error) {
        console.error('Error cargando clientes:', error);
    }
}

// Cargar clientes al inicializar
document.addEventListener('DOMContentLoaded', cargarClientesEnMapa);
</script>