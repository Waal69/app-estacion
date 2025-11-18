// Configuración de la API
const API_BASE = 'https://mattprofe.com.ar/proyectos/app-estacion/';

// Función para cargar estaciones
async function cargarEstaciones() {
    try {
        const response = await fetch(`${API_BASE}api/estaciones`);
        const estaciones = await response.json();
        
        mostrarEstaciones(estaciones);
    } catch (error) {
        console.error('Error al cargar estaciones:', error);
        document.getElementById('loading').textContent = 'Error al cargar las estaciones';
    }
}

// Función para mostrar estaciones usando template
function mostrarEstaciones(estaciones) {
    const container = document.getElementById('estaciones-container');
    const template = document.getElementById('estacion-template');
    const loading = document.getElementById('loading');
    
    // Limpiar container
    container.innerHTML = '';
    
    estaciones.forEach(estacion => {
        // Clonar template
        const clone = template.content.cloneNode(true);
        
        // Llenar datos
        clone.querySelector('.estacion-card').setAttribute('data-chipid', estacion.chipid);
        clone.querySelector('.apodo').textContent = estacion.apodo || 'Sin nombre';
        clone.querySelector('.ubicacion').textContent = estacion.ubicacion || 'Sin ubicación';
        clone.querySelector('.contador-visitas').textContent = estacion.visitas || '0';
        
        // Agregar evento click
        const card = clone.querySelector('.estacion-card');
        card.addEventListener('click', () => {
            window.location.href = `/app-estacion/detalle/${estacion.chipid}`;
        });
        
        container.appendChild(clone);
    });
    
    // Ocultar loading y mostrar estaciones
    loading.style.display = 'none';
    container.style.display = 'grid';
}

// Función para cargar detalle de estación específica
async function cargarDetalleEstacion(chipid) {
    try {
        const response = await fetch(`${API_BASE}api/estacion/${chipid}`);
        const estacion = await response.json();
        
        document.getElementById('estacion-apodo').textContent = estacion.apodo || 'Sin nombre';
        document.getElementById('estacion-ubicacion').textContent = estacion.ubicacion || 'Sin ubicación';
    } catch (error) {
        console.error('Error al cargar detalle:', error);
        document.getElementById('estacion-apodo').textContent = 'Error al cargar';
        document.getElementById('estacion-ubicacion').textContent = 'Error al cargar';
    }
}

// Cargar estaciones si estamos en la página del panel
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('estaciones-container')) {
        cargarEstaciones();
    }
});