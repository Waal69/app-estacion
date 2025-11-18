<div class="admin-container">
    <div class="admin-header">
        <h2>Panel de Administrador</h2>
        <a href="/app-estacion/admin-logout" class="btn-logout">Cerrar Sesión</a>
    </div>
    
    <div class="admin-content">
        <div class="admin-actions">
            <a href="/app-estacion/map" class="btn-primary btn-large">
                🗺️ Mapa de Clientes
            </a>
        </div>
        
        <div class="admin-stats">
            <div class="stat-card">
                <h3>👥 Usuarios Registrados</h3>
                <div class="stat-number"><?php echo $usuarios; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>🌍 Clientes Únicos</h3>
                <div class="stat-number"><?php echo $clientes; ?></div>
            </div>
        </div>
    </div>
</div>