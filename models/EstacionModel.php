<?php
class EstacionModel {
    
    public function getEstacionByChipid($chipid) {
        // Simulación de datos de estación
        return [
            'chipid' => $chipid,
            'apodo' => 'Estación ' . $chipid,
            'ubicacion' => 'Ubicación de la estación ' . $chipid
        ];
    }
}