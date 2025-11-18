<?php
require_once 'config/View.php';
require_once 'config/Auth.php';
require_once 'models/EstacionModel.php';

class EstacionController {
    
    public function landing() {
        $view = new View('landing');
        $view->assign('title', 'Inicio - ' . APP_NAME);
        $view->render();
    }
    
    public function panel() {
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
}