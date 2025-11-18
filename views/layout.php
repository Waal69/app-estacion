<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?></title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <h1><a href="/app-estacion/"><?php echo APP_NAME; ?></a></h1>
            <div class="nav-links">
                <?php 
                require_once 'config/Auth.php';
                if (Auth::estaLogueado()): 
                    $usuario = Auth::getUsuario();
                ?>
                    <span>Hola, <?php echo htmlspecialchars($usuario['nombres']); ?></span>
                    <a href="/app-estacion/logout" class="btn-logout">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/app-estacion/login" class="btn-login">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <footer>
        <p>&copy; 2024 App Estación Meteorológica</p>
    </footer>
    
    <script src="public/js/app.js"></script>
</body>
</html>