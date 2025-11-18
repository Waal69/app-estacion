<div class="auth-container">
    <div class="auth-card">
        <h2>Restablecer Contraseña</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="contraseña">Nueva Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <div class="form-group">
                <label for="repetir_contraseña">Repetir Contraseña:</label>
                <input type="password" id="repetir_contraseña" name="repetir_contraseña" required>
            </div>
            
            <button type="submit" class="btn-primary">Restablecer</button>
        </form>
        
        <div class="auth-links">
            <a href="/app-estacion/login">← Volver al login</a>
        </div>
    </div>
</div>