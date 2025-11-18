<div class="auth-container">
    <div class="auth-card">
        <h2>Acceso de Administrador</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <button type="submit" class="btn-primary">Acceder como Admin</button>
        </form>
        
        <div class="auth-links">
            <a href="/app-estacion/panel">← Volver al panel</a>
        </div>
    </div>
</div>