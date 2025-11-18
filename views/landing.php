<div class="landing">
    <div class="hero">
        <h2>Bienvenido a App Estación</h2>
        <p>Sistema de monitoreo de estaciones meteorológicas en tiempo real.</p>
        <p>Consulta datos de temperatura, humedad, viento y más de nuestras estaciones distribuidas.</p>
        
        <div class="features">
            <div class="feature">
                <h3>🌡️ Temperatura</h3>
                <p>Monitoreo en tiempo real</p>
            </div>
            <div class="feature">
                <h3>💧 Humedad</h3>
                <p>Niveles de humedad ambiente</p>
            </div>
            <div class="feature">
                <h3>🌪️ Viento</h3>
                <p>Velocidad y dirección</p>
            </div>
        </div>
        
        <?php 
        require_once 'config/Auth.php';
        if (Auth::estaLogueado()): 
        ?>
            <a href="/app-estacion/panel" class="btn-primary">Ver Panel de Estaciones</a>
        <?php else: ?>
            <a href="/app-estacion/login" class="btn-primary">Iniciar Sesión</a>
            <a href="/app-estacion/register" class="btn-secondary">Registrarse</a>
        <?php endif; ?>
    </div>
</div>