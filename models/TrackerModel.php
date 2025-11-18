<?php
require_once 'config/Database.php';

class TrackerModel {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function registrarAcceso($ip, $latitud, $longitud, $pais, $navegador, $sistema) {
        $token = bin2hex(random_bytes(16));
        
        $sql = "INSERT INTO tracker (token, ip, latitud, longitud, pais, navegador, sistema) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($sql);
        
        return $stmt->execute([$token, $ip, $latitud, $longitud, $pais, $navegador, $sistema]);
    }
    
    public function getClientesUbicacion() {
        $sql = "SELECT ip, latitud, longitud, COUNT(*) as accesos 
                FROM tracker 
                GROUP BY ip, latitud, longitud";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarUsuarios() {
        $sql = "SELECT COUNT(DISTINCT id) as total FROM usuarios";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function contarClientes() {
        $sql = "SELECT COUNT(DISTINCT ip) as total FROM tracker";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}