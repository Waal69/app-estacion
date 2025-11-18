<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require_once 'vendor/autoload.php'; // Descomenta cuando instales PHPMailer

class Mailer {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurar();
    }
    
    private function configurar() {
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'appestacion2024@gmail.com'; // Crear este email
        $this->mail->Password = 'app_password_2024'; // App password de Gmail
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom('appestacion2024@gmail.com', 'App Estación');
    }
    
    public function enviarEmail($destinatario, $asunto, $cuerpo) {
        try {
            $this->mail->addAddress($destinatario);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = $cuerpo;
            
            $this->mail->send();
            $this->mail->clearAddresses();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}