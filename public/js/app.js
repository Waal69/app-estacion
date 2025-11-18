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

// Inicializar gráficos Chart.js
function inicializarGraficos() {
    const configBase = {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Temperatura
    graficos.temperatura = new Chart(document.getElementById('graficoTemperatura'), {
        ...configBase,
        data: {
            labels: [],
            datasets: [{
                label: 'Temperatura (°C)',
                data: [],
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                tension: 0.4
            }]
        }
    });

    // Humedad
    graficos.humedad = new Chart(document.getElementById('graficoHumedad'), {
        ...configBase,
        data: {
            labels: [],
            datasets: [{
                label: 'Humedad (%)',
                data: [],
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.1)',
                tension: 0.4
            }]
        }
    });

    // Viento
    graficos.viento = new Chart(document.getElementById('graficoViento'), {
        ...configBase,
        data: {
            labels: [],
            datasets: [{
                label: 'Viento (km/h)',
                data: [],
                borderColor: '#45b7d1',
                backgroundColor: 'rgba(69, 183, 209, 0.1)',
                tension: 0.4
            }]
        }
    });

    // Presión
    graficos.presion = new Chart(document.getElementById('graficoPresion'), {
        ...configBase,
        data: {
            labels: [],
            datasets: [{
                label: 'Presión (hPa)',
                data: [],
                borderColor: '#f7b731',
                backgroundColor: 'rgba(247, 183, 49, 0.1)',
                tension: 0.4
            }]
        }
    });

    // Riesgo de incendio
    graficos.incendio = new Chart(document.getElementById('graficoIncendio'), {
        ...configBase,
        data: {
            labels: [],
            datasets: [{
                label: 'Riesgo (%)',
                data: [],
                borderColor: '#e17055',
                backgroundColor: 'rgba(225, 112, 85, 0.1)',
                tension: 0.4
            }]
        }
    });
}

// Cargar datos completos y actualizar gráficos
async function cargarDatosCompletos() {
    try {
        // Cargar información básica
        const responseInfo = await fetch(`${API_BASE}api/estacion/${chipid}`);
        const estacion = await responseInfo.json();
        
        document.getElementById('estacion-apodo').textContent = estacion.apodo || 'Sin nombre';
        document.getElementById('estacion-ubicacion').textContent = estacion.ubicacion || 'Sin ubicación';
        
        // Cargar datos de sensores
        const responseDatos = await fetch(`${API_BASE}api/datos/${chipid}`);
        const datos = await responseDatos.json();
        
        if (datos && datos.length > 0) {
            actualizarGraficos(datos);
            actualizarValoresActuales(datos[datos.length - 1]);
        }
        
        // Actualizar timestamp
        document.getElementById('ultima-actualizacion').textContent = new Date().toLocaleTimeString();
        
    } catch (error) {
        console.error('Error al cargar datos:', error);
    }
}

// Actualizar gráficos con nuevos datos
function actualizarGraficos(datos) {
    const labels = datos.map(d => new Date(d.fecha).toLocaleTimeString());
    
    // Mantener solo los últimos 10 puntos
    const maxPuntos = 10;
    const datosRecientes = datos.slice(-maxPuntos);
    const labelsRecientes = labels.slice(-maxPuntos);
    
    // Temperatura
    graficos.temperatura.data.labels = labelsRecientes;
    graficos.temperatura.data.datasets[0].data = datosRecientes.map(d => parseFloat(d.temperatura));
    graficos.temperatura.update();
    
    // Humedad
    graficos.humedad.data.labels = labelsRecientes;
    graficos.humedad.data.datasets[0].data = datosRecientes.map(d => parseFloat(d.humedad));
    graficos.humedad.update();
    
    // Viento
    graficos.viento.data.labels = labelsRecientes;
    graficos.viento.data.datasets[0].data = datosRecientes.map(d => parseFloat(d.viento));
    graficos.viento.update();
    
    // Presión (simulada si no existe)
    graficos.presion.data.labels = labelsRecientes;
    graficos.presion.data.datasets[0].data = datosRecientes.map(d => 
        d.presion ? parseFloat(d.presion) : 1013 + Math.random() * 20
    );
    graficos.presion.update();
    
    // Incendio
    graficos.incendio.data.labels = labelsRecientes;
    graficos.incendio.data.datasets[0].data = datosRecientes.map(d => parseFloat(d.incendio || 0));
    graficos.incendio.update();
}

// Actualizar valores actuales mostrados
function actualizarValoresActuales(ultimoDato) {
    document.getElementById('temp-actual').textContent = parseFloat(ultimoDato.temperatura).toFixed(1);
    document.getElementById('humedad-actual').textContent = parseFloat(ultimoDato.humedad).toFixed(1);
    document.getElementById('viento-actual').textContent = parseFloat(ultimoDato.viento).toFixed(1);
    document.getElementById('presion-actual').textContent = ultimoDato.presion ? 
        parseFloat(ultimoDato.presion).toFixed(0) : (1013 + Math.random() * 20).toFixed(0);
    document.getElementById('incendio-actual').textContent = parseFloat(ultimoDato.incendio || 0).toFixed(1);
}

// Cargar estaciones si estamos en la página del panel
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('estaciones-container')) {
        cargarEstaciones();
    }
});