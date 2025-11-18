<?php
require_once 'config/View.php';
require_once 'config/Auth.php';
require_once 'config/Mailer.php';
require_once 'models/UsuarioModel.php';

class AuthController {
    private $usuarioModel;
    private $mailer;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->mailer = new Mailer();
    }
    
    public function login() {
        if (Auth::estaLogueado()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $contraseña = $_POST['contraseña'] ?? '';
            
            $usuario = $this->usuarioModel->buscarPorEmail($email);
            
            if ($usuario) {
                if ($this->usuarioModel->verificarContraseña($contraseña, $usuario['contraseña'])) {
                    if (!$usuario['activo']) {
                        $error = 'Su usuario aún no se ha validado, revise su casilla de correo';
                    } elseif ($usuario['bloqueado'] || $usuario['recupero']) {
                        $error = 'Su usuario está bloqueado, revise su casilla de correo';
                    } else {
                        Auth::login($usuario);
                        $this->enviarNotificacionLogin($usuario);
                        
                        $chipid = $_GET['chipid'] ?? '3099812';
                        header("Location: /app-estacion/detalle/$chipid");
                        exit;
                    }
                } else {
                    $this->enviarNotificacionIntentoFallido($usuario);
                    $error = 'Credenciales no válidas';
                }
            } else {
                $error = 'Credenciales no válidas';
            }
        }
        
        $view = new View('login');
        $view->assign('title', 'Iniciar Sesión - ' . APP_NAME);
        $view->assign('error', $error ?? '');
        $view->render();
    }
    
    public function register() {
        if (Auth::estaLogueado()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $nombres = $_POST['nombres'] ?? '';
            $contraseña = $_POST['contraseña'] ?? '';
            $repetir_contraseña = $_POST['repetir_contraseña'] ?? '';
            
            if ($contraseña !== $repetir_contraseña) {
                $error = 'Las contraseñas no coinciden';
            } elseif ($this->usuarioModel->buscarPorEmail($email)) {
                $error = 'El email ya está registrado. <a href="/app-estacion/login">Iniciar sesión</a>';
            } else {
                $tokens = $this->usuarioModel->crearUsuario($email, $nombres, $contraseña);
                if ($tokens) {
                    $this->enviarEmailActivacion($email, $nombres, $tokens['token_action']);
                    $success = 'Usuario registrado. Revise su correo para activar la cuenta.';
                } else {
                    $error = 'Error al registrar usuario';
                }
            }
        }
        
        $view = new View('register');
        $view->assign('title', 'Registrarse - ' . APP_NAME);
        $view->assign('error', $error ?? '');
        $view->assign('success', $success ?? '');
        $view->render();
    }
    
    public function validate($token_action) {
        if (Auth::estaLogueado()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        $usuario = $this->usuarioModel->buscarPorTokenAction($token_action);
        
        if ($usuario && !$usuario['activo']) {
            if ($this->usuarioModel->activarUsuario($token_action)) {
                $this->enviarEmailUsuarioActivo($usuario['email'], $usuario['nombres']);
                header('Location: /app-estacion/login?activated=1');
                exit;
            }
        }
        
        $view = new View('mensaje');
        $view->assign('title', 'Validación - ' . APP_NAME);
        $view->assign('mensaje', 'El token no corresponde a un usuario');
        $view->render();
    }
    
    public function blocked($token) {
        $usuario = $this->usuarioModel->buscarPorToken($token);
        
        if ($usuario) {
            $token_action = $this->usuarioModel->bloquearUsuario($token);
            if ($token_action) {
                $this->enviarEmailUsuarioBloqueado($usuario['email'], $usuario['nombres'], $token_action);
                $mensaje = 'Usuario bloqueado, revise su correo electrónico';
            } else {
                $mensaje = 'Error al bloquear usuario';
            }
        } else {
            $mensaje = 'El token no corresponde a un usuario';
        }
        
        $view = new View('mensaje');
        $view->assign('title', 'Usuario Bloqueado - ' . APP_NAME);
        $view->assign('mensaje', $mensaje);
        $view->render();
    }
    
    public function recovery() {
        if (Auth::estaLogueado()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $usuario = $this->usuarioModel->buscarPorEmail($email);
            
            if ($usuario) {
                $token_action = $this->usuarioModel->iniciarRecupero($email);
                if ($token_action) {
                    $this->enviarEmailRecuperacion($email, $usuario['nombres'], $token_action);
                    $success = 'Se ha enviado un email para restablecer su contraseña';
                }
            } else {
                $error = 'El email no se encuentra registrado. <a href="/app-estacion/register">Registrarse</a>';
            }
        }
        
        $view = new View('recovery');
        $view->assign('title', 'Recuperar Contraseña - ' . APP_NAME);
        $view->assign('error', $error ?? '');
        $view->assign('success', $success ?? '');
        $view->render();
    }
    
    public function reset($token_action) {
        if (Auth::estaLogueado()) {
            header('Location: /app-estacion/panel');
            exit;
        }
        
        $usuario = $this->usuarioModel->buscarPorTokenAction($token_action);
        
        if (!$usuario) {
            $view = new View('mensaje');
            $view->assign('title', 'Reset - ' . APP_NAME);
            $view->assign('mensaje', 'El token no es válido');
            $view->render();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contraseña = $_POST['contraseña'] ?? '';
            $repetir_contraseña = $_POST['repetir_contraseña'] ?? '';
            
            if ($contraseña !== $repetir_contraseña) {
                $error = 'Las contraseñas no coinciden';
            } else {
                if ($this->usuarioModel->resetearContraseña($token_action, $contraseña)) {
                    $this->enviarEmailContraseñaRestablecida($usuario['email'], $usuario['nombres'], $usuario['token']);
                    header('Location: /app-estacion/login?reset=1');
                    exit;
                } else {
                    $error = 'Error al restablecer contraseña';
                }
            }
        }
        
        $view = new View('reset');
        $view->assign('title', 'Restablecer Contraseña - ' . APP_NAME);
        $view->assign('error', $error ?? '');
        $view->assign('token_action', $token_action);
        $view->render();
    }
    
    public function logout() {
        Auth::logout();
        header('Location: /app-estacion/login');
        exit;
    }
    
    private function enviarNotificacionLogin($usuario) {
        $info = Auth::getClientInfo();
        $asunto = 'Inicio de sesión en App Estación';
        $cuerpo = "
        <h2>Inicio de sesión detectado</h2>
        <p>Hola {$usuario['nombres']},</p>
        <p>Se ha iniciado sesión en tu cuenta desde:</p>
        <ul>
            <li><strong>IP:</strong> {$info['ip']}</li>
            <li><strong>Sistema:</strong> {$info['os']}</li>
            <li><strong>Navegador:</strong> {$info['browser']}</li>
        </ul>
        <p>Si no fuiste tú:</p>
        <a href='" . BASE_URL . "blocked/{$usuario['token']}' style='background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>No fui yo, bloquear cuenta</a>
        ";
        
        $this->mailer->enviarEmail($usuario['email'], $asunto, $cuerpo);
    }
    
    private function enviarNotificacionIntentoFallido($usuario) {
        $info = Auth::getClientInfo();
        $asunto = 'Intento de acceso fallido';
        $cuerpo = "
        <h2>Intento de acceso con contraseña inválida</h2>
        <p>Hola {$usuario['nombres']},</p>
        <p>Se detectó un intento de acceso fallido desde:</p>
        <ul>
            <li><strong>IP:</strong> {$info['ip']}</li>
            <li><strong>Sistema:</strong> {$info['os']}</li>
            <li><strong>Navegador:</strong> {$info['browser']}</li>
        </ul>
        <p>Si no fuiste tú:</p>
        <a href='" . BASE_URL . "blocked/{$usuario['token']}' style='background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>No fui yo, bloquear cuenta</a>
        ";
        
        $this->mailer->enviarEmail($usuario['email'], $asunto, $cuerpo);
    }
    
    private function enviarEmailActivacion($email, $nombres, $token_action) {
        $asunto = 'Bienvenido a App Estación';
        $cuerpo = "
        <h2>¡Bienvenido a App Estación!</h2>
        <p>Hola $nombres,</p>
        <p>Gracias por registrarte en nuestra aplicación de estaciones meteorológicas.</p>
        <p>Para activar tu cuenta, haz clic en el siguiente botón:</p>
        <a href='" . BASE_URL . "validate/$token_action' style='background: #27ae60; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>Click aquí para activar tu usuario</a>
        ";
        
        $this->mailer->enviarEmail($email, $asunto, $cuerpo);
    }
    
    private function enviarEmailUsuarioActivo($email, $nombres) {
        $asunto = 'Cuenta activada - App Estación';
        $cuerpo = "
        <h2>¡Tu cuenta está activa!</h2>
        <p>Hola $nombres,</p>
        <p>Tu cuenta en App Estación ha sido activada exitosamente.</p>
        <p>Ya puedes iniciar sesión y disfrutar de todas las funcionalidades.</p>
        ";
        
        $this->mailer->enviarEmail($email, $asunto, $cuerpo);
    }
    
    private function enviarEmailUsuarioBloqueado($email, $nombres, $token_action) {
        $asunto = 'Cuenta bloqueada - App Estación';
        $cuerpo = "
        <h2>Tu cuenta ha sido bloqueada</h2>
        <p>Hola $nombres,</p>
        <p>Tu cuenta en App Estación ha sido bloqueada por seguridad.</p>
        <p>Para cambiar tu contraseña y desbloquear tu cuenta:</p>
        <a href='" . BASE_URL . "reset/$token_action' style='background: #f39c12; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>Click aquí para cambiar contraseña</a>
        ";
        
        $this->mailer->enviarEmail($email, $asunto, $cuerpo);
    }
    
    private function enviarEmailRecuperacion($email, $nombres, $token_action) {
        $asunto = 'Restablecer contraseña - App Estación';
        $cuerpo = "
        <h2>Restablecimiento de contraseña</h2>
        <p>Hola $nombres,</p>
        <p>Has solicitado restablecer tu contraseña en App Estación.</p>
        <p>Para continuar con el proceso:</p>
        <a href='" . BASE_URL . "reset/$token_action' style='background: #3498db; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;'>Click aquí para restablecer contraseña</a>
        ";
        
        $this->mailer->enviarEmail($email, $asunto, $cuerpo);
    }
    
    private function enviarEmailContraseñaRestablecida($email, $nombres, $token) {
        $info = Auth::getClientInfo();
        $asunto = 'Contraseña restablecida - App Estación';
        $cuerpo = "
        <h2>Contraseña restablecida exitosamente</h2>
        <p>Hola $nombres,</p>
        <p>Tu contraseña ha sido restablecida desde:</p>
        <ul>
            <li><strong>IP:</strong> {$info['ip']}</li>
            <li><strong>Sistema:</strong> {$info['os']}</li>
            <li><strong>Navegador:</strong> {$info['browser']}</li>
        </ul>
        <p>Si no fuiste tú:</p>
        <a href='" . BASE_URL . "blocked/$token' style='background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>No fui yo, bloquear cuenta</a>
        ";
        
        $this->mailer->enviarEmail($email, $asunto, $cuerpo);
    }
}