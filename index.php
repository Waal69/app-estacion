<?php
require_once 'env.php';
require_once 'config/Router.php';
require_once 'controllers/EstacionController.php';

$router = new Router();

// Rutas
$router->add('/', 'EstacionController', 'landing');
$router->add('/panel', 'EstacionController', 'panel');
$router->add('/detalle/{id}', 'EstacionController', 'detalle');

$router->dispatch();
?>