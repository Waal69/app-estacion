<div class="detalle">
    <h2>Detalle de Estación</h2>
    
    <div class="estacion-info">
        <h3>Información de la Estación</h3>
        <div class="info-card">
            <p><strong>Chip ID:</strong> <?php echo htmlspecialchars($chipid); ?></p>
            <p><strong>Apodo:</strong> <span id="estacion-apodo">Cargando...</span></p>
            <p><strong>Ubicación:</strong> <span id="estacion-ubicacion">Cargando...</span></p>
        </div>
    </div>
    
    <div class="acciones">
        <a href="/app-estacion/panel" class="btn-secondary">← Volver al Panel</a>
    </div>
</div>

<script>
// Cargar datos específicos de la estación
document.addEventListener('DOMContentLoaded', function() {
    const chipid = '<?php echo htmlspecialchars($chipid); ?>';
    cargarDetalleEstacion(chipid);
});
</script>