<div class="detalle">
    <h2>Detalle de Estación</h2>
    
    <div class="estacion-info">
        <h3>Información de la Estación</h3>
        <div class="info-card">
            <p><strong>Chip ID:</strong> <?php echo htmlspecialchars($chipid); ?></p>
            <p><strong>Apodo:</strong> <span id="estacion-apodo">Cargando...</span></p>
            <p><strong>Ubicación:</strong> <span id="estacion-ubicacion">Cargando...</span></p>
            <p><strong>Última actualización:</strong> <span id="ultima-actualizacion">-</span></p>
        </div>
    </div>

    <div class="graficos-container">
        <div class="grafico-item">
            <h3>🌡️ Temperatura</h3>
            <canvas id="graficoTemperatura"></canvas>
            <div class="valor-actual">
                <span id="temp-actual">--</span>°C
            </div>
        </div>

        <div class="grafico-item">
            <h3>💧 Humedad</h3>
            <canvas id="graficoHumedad"></canvas>
            <div class="valor-actual">
                <span id="humedad-actual">--</span>%
            </div>
        </div>

        <div class="grafico-item">
            <h3>🌪️ Viento</h3>
            <canvas id="graficoViento"></canvas>
            <div class="valor-actual">
                <span id="viento-actual">--</span> km/h
            </div>
        </div>

        <div class="grafico-item">
            <h3>🌊 Presión Atmosférica</h3>
            <canvas id="graficoPresion"></canvas>
            <div class="valor-actual">
                <span id="presion-actual">--</span> hPa
            </div>
        </div>

        <div class="grafico-item">
            <h3>🔥 Riesgo de Incendio</h3>
            <canvas id="graficoIncendio"></canvas>
            <div class="valor-actual">
                <span id="incendio-actual">--</span>%
            </div>
        </div>
    </div>
    
    <div class="acciones">
        <a href="/app-estacion/panel" class="btn-secondary">← Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chipid = '<?php echo htmlspecialchars($chipid); ?>';
let graficos = {};
let intervalId;

// Inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficos();
    cargarDatosCompletos();
    
    // Actualizar cada 60 segundos
    intervalId = setInterval(cargarDatosCompletos, 60000);
});

// Limpiar interval al salir de la página
window.addEventListener('beforeunload', function() {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>