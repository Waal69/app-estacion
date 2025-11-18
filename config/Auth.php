<?php
class Auth {
    public static function iniciarSesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function login($usuario) {
        self::iniciarSesion();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_token'] = $usuario['token'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nombres'] = $usuario['nombres'];
    }
    
    public static function logout() {
        self::iniciarSesion();
        session_destroy();
    }
    
    public static function estaLogueado() {
        self::iniciarSesion();
        return isset($_SESSION['usuario_id']);
    }
    
    public static function getUsuario() {
        self::iniciarSesion();
        if (self::estaLogueado()) {
            return [
                'id' => $_SESSION['usuario_id'],
                'token' => $_SESSION['usuario_token'],
                'email' => $_SESSION['usuario_email'],
                'nombres' => $_SESSION['usuario_nombres']
            ];
        }
        return null;
    }
    
    public static function getClientInfo() {
        return [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Desconocida',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido',
            'os' => self::getOS(),
            'browser' => self::getBrowser()
        ];
    }
    
    private static function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $os_array = [
            '/windows nt 10/i'      => 'Windows 10',
            '/windows nt 6.3/i'     => 'Windows 8.1',
            '/windows nt 6.2/i'     => 'Windows 8',
            '/windows nt 6.1/i'     => 'Windows 7',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
        ];
        
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                return $value;
            }
        }
        return 'Desconocido';
    }
    
    private static function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser_array = [
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
        ];
        
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                return $value;
            }
        }
        return 'Desconocido';
    }
}