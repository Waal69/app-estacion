<div class="panel">
    <h2>Panel de Estaciones Meteorológicas</h2>
    <p>Selecciona una estación para ver sus detalles:</p>
    
    <div id="loading">Cargando estaciones...</div>
    
    <div id="estaciones-container" class="estaciones-grid" style="display: none;">
        <!-- Las estaciones se cargarán aquí via JavaScript -->
    </div>
    
    <template id="estacion-template">
        <div class="estacion-card" data-chipid="">
            <h3 class="apodo"></h3>
            <p class="ubicacion"></p>
            <p class="visitas">Visitas: <span class="contador-visitas"></span></p>
            <button class="btn-detalle">Ver Detalle</button>
        </div>
    </template>
</div>