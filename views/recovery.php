<div class="auth-container">
    <div class="auth-card">
        <h2>Recuperar Contraseña</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <small>Ingresa tu email para recibir instrucciones de recuperación</small>
                </div>
                
                <button type="submit" class="btn-primary">Enviar</button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <a href="/app-estacion/login">← Volver al login</a>
        </div>
    </div>
</div>