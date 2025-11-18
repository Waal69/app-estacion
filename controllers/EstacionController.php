<?php
require_once 'config/View.php';
require_once 'config/Auth.php';
require_once 'models/EstacionModel.php';
require_once 'models/TrackerModel.php';

class EstacionController {
    private $trackerModel;
    
    public function __construct() {
        $this->trackerModel = new TrackerModel();
    }
    
    public function landing() {
        $view = new View('landing');
        $view->assign('title', 'Inicio - ' . APP_NAME);
        $view->render();
    }
    
    public function panel() {
        // Registrar acceso del cliente
        $this->registrarAccesoCliente();
        
        $view = new View('panel');
        $view->assign('title', 'Panel de Estaciones - ' . APP_NAME);
        $view->assign('usuario_logueado', Auth::estaLogueado());
        $view->render();
    }
    
    public function detalle($chipid = null) {
        // Verificar que el usuario esté logueado
        if (!Auth::estaLogueado()) {
            header('Location: /app-estacion/login?chipid=' . urlencode($chipid));
            exit;
        }
        
        if (!$chipid) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        $model = new EstacionModel();
        $estacion = $model->getEstacionByChipid($chipid);
        
        $view = new View('detalle');
        $view->assign('title', 'Detalle de Estación - ' . APP_NAME);
        $view->assign('estacion', $estacion);
        $view->assign('chipid', $chipid);
        $view->assign('usuario', Auth::getUsuario());
        $view->render();
    }
    
    private function registrarAccesoCliente() {
        $ip = $this->getClientIP();
        
        // Obtener información de geolocalización
        $geoData = $this->getGeoData($ip);
        
        if ($geoData) {
            $info = Auth::getClientInfo();
            
            $this->trackerModel->registrarAcceso(
                $ip,
                $geoData['latitude'] ?? '0',
                $geoData['longitude'] ?? '0',
                $geoData['country'] ?? 'Desconocido',
                $info['browser'],
                $info['os']
            );
        }
    }
    
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    private function getGeoData($ip) {
        // Para IPs locales, usar datos de ejemplo
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
            return [
                'latitude' => '-34.6118',
                'longitude' => '-58.3960',
                'country' => 'Argentina'
            ];
        }
        
        try {
            $url = "http://ipwho.is/$ip";
            $response = file_get_contents($url);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                
                if ($data && $data['success'] === true) {
                    return [
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'country' => $data['country']
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Error obteniendo geodatos: " . $e->getMessage());
        }
        
        // Datos por defecto si falla la API
        return [
            'latitude' => '0',
            'longitude' => '0',
            'country' => 'Desconocido'
        ];
    }
}