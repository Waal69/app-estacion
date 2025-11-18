<div class="auth-container">
    <div class="auth-card">
        <h2>Registrarse</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="nombres">Nombres:</label>
                    <input type="text" id="nombres" name="nombres" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                
                <div class="form-group">
                    <label for="repetir_contraseña">Repetir Contraseña:</label>
                    <input type="password" id="repetir_contraseña" name="repetir_contraseña" required>
                </div>
                
                <button type="submit" class="btn-primary">Registrarse</button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <p>¿Ya tienes una cuenta? <a href="/app-estacion/login">Iniciar sesión</a></p>
        </div>
    </div>
</div>