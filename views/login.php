<div class="auth-container">
    <div class="auth-card">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($_GET['activated'])): ?>
            <div class="alert alert-success">¡Cuenta activada exitosamente! Ya puedes iniciar sesión.</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['reset'])): ?>
            <div class="alert alert-success">Contraseña restablecida exitosamente.</div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <button type="submit" class="btn-primary">Acceder</button>
        </form>
        
        <div class="auth-links">
            <a href="/app-estacion/recovery">¿Olvidaste tu contraseña?</a>
            <p>¿No tienes una cuenta? <a href="/app-estacion/register">Registrarse</a></p>
        </div>
    </div>
</div>