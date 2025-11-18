<?php
require_once 'config/Database.php';

class UsuarioModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function crearUsuario($email, $nombres, $contraseña) {
        $token = bin2hex(random_bytes(32));
        $token_action = bin2hex(random_bytes(32));
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (token, email, nombres, contraseña, token_action) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if ($stmt->execute([$token, $email, $nombres, $contraseña_hash, $token_action])) {
            return ['token' => $token, 'token_action' => $token_action];
        }
        return false;
    }
    
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorToken($token) {
        $sql = "SELECT * FROM usuarios WHERE token = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorTokenAction($token_action) {
        $sql = "SELECT * FROM usuarios WHERE token_action = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$token_action]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function activarUsuario($token_action) {
        $sql = "UPDATE usuarios SET activo = 1, token_action = NULL, active_date = NOW() WHERE token_action = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$token_action]);
    }
    
    public function bloquearUsuario($token) {
        $token_action = bin2hex(random_bytes(32));
        $sql = "UPDATE usuarios SET bloqueado = 1, token_action = ?, blocked_date = NOW() WHERE token = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if ($stmt->execute([$token_action, $token])) {
            return $token_action;
        }
        return false;
    }
    
    public function iniciarRecupero($email) {
        $token_action = bin2hex(random_bytes(32));
        $sql = "UPDATE usuarios SET recupero = 1, token_action = ?, recover_date = NOW() WHERE email = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        if ($stmt->execute([$token_action, $email])) {
            return $token_action;
        }
        return false;
    }
    
    public function resetearContraseña($token_action, $nueva_contraseña) {
        $contraseña_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET contraseña = ?, token_action = NULL, bloqueado = 0, recupero = 0 WHERE token_action = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$contraseña_hash, $token_action]);
    }
    
    public function verificarContraseña($contraseña, $hash) {
        return password_verify($contraseña, $hash);
    }
}