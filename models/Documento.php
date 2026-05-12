<?php
// models/Documento.php

class Documento {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listarConCalculo() {
        // SQL definitivo con nombres de columnas reales
        $sql = "SELECT 
                    d.id, 
                    p.nombre AS proveedor, 
                    d.valor_total, 
                    r.base_uvt,
                    (SELECT valor FROM variables_sistema WHERE clave = 'VALOR_UVT') AS uvt_actual
                FROM documentos d
                JOIN proveedores p ON d.id_proveedor = p.id
                JOIN retenciones r ON p.id_retencion_aplicable = r.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}