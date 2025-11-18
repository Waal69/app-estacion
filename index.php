<?php
require_once 'env.php';
require_once 'config/Router.php';
require_once 'controllers/EstacionController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';

$router = new Router();

// Rutas públicas
$router->add('/', 'EstacionController', 'landing');
$router->add('/panel', 'EstacionController', 'panel');

// Rutas de autenticación
$router->add('/login', 'AuthController', 'login');
$router->add('/register', 'AuthController', 'register');
$router->add('/validate/{token}', 'AuthController', 'validate');
$router->add('/blocked/{token}', 'AuthController', 'blocked');
$router->add('/recovery', 'AuthController', 'recovery');
$router->add('/reset/{token}', 'AuthController', 'reset');
$router->add('/logout', 'AuthController', 'logout');

// Rutas protegidas
$router->add('/detalle/{id}', 'EstacionController', 'detalle');

// Rutas de administrador
$router->add('/admin-login', 'AdminController', 'adminLogin');
$router->add('/administrator', 'AdminController', 'administrator');
$router->add('/map', 'AdminController', 'map');
$router->add('/admin-logout', 'AdminController', 'adminLogout');
$router->add('/api/clients-location', 'AdminController', 'apiClientsLocation');

$router->dispatch();
?>