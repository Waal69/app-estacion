<?php
require_once 'config/View.php';
require_once 'config/Auth.php';
require_once 'models/TrackerModel.php';

class AdminController {
    private $trackerModel;
    
    public function __construct() {
        $this->trackerModel = new TrackerModel();
    }
    
    public function administrator() {
        if (!$this->esAdmin()) {
            header('Location: /app-estacion/admin-login');
            exit;
        }
        
        $usuarios = $this->trackerModel->contarUsuarios();
        $clientes = $this->trackerModel->contarClientes();
        
        $view = new View('administrator');
        $view->assign('title', 'Administrador - ' . APP_NAME);
        $view->assign('usuarios', $usuarios);
        $view->assign('clientes', $clientes);
        $view->render();
    }
    
    public function map() {
        if (!$this->esAdmin()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        $view = new View('map');
        $view->assign('title', 'Mapa de Clientes - ' . APP_NAME);
        $view->render();
    }
    
    public function adminLogin() {
        if ($this->esAdmin()) {
            header('Location: /app-estacion/administrator');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? '';
            $contraseña = $_POST['contraseña'] ?? '';
            
            if ($usuario === 'admin-estacion' && $contraseña === 'admin1234') {
                Auth::iniciarSesion();
                $_SESSION['admin'] = true;
                header('Location: /app-estacion/administrator');
                exit;
            } else {
                $error = 'Credenciales de administrador incorrectas';
            }
        }
        
        $view = new View('admin-login');
        $view->assign('title', 'Admin Login - ' . APP_NAME);
        $view->assign('error', $error ?? '');
        $view->render();
    }
    
    public function adminLogout() {
        Auth::iniciarSesion();
        unset($_SESSION['admin']);
        header('Location: /app-estacion/panel');
        exit;
    }
    
    public function apiClientsLocation() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['list-clients-location'])) {
            echo json_encode(['error' => 'Parámetro requerido']);
            return;
        }
        
        $clientes = $this->trackerModel->getClientesUbicacion();
        
        $resultado = array_map(function($cliente) {
            return [
                'ip' => $cliente['ip'],
                'latitud' => $cliente['latitud'],
                'longitud' => $cliente['longitud'],
                'accesos' => (int)$cliente['accesos']
            ];
        }, $clientes);
        
        echo json_encode($resultado);
    }
    
    private function esAdmin() {
        Auth::iniciarSesion();
        return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
    }
}