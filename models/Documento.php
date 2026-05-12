<?php
class Documento {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listarConCalculo() {
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
        return $stmt->fetchAll();
    }

    public function guardarProcesado($id, $aplica) {
        // SEGURIDAD: Consulta preparada obligatoria
        $sql = "INSERT INTO documentos_procesados (documento_id, aplica, fecha_proceso) 
                VALUES (:id, :aplica, DATETIME('now'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':aplica', $aplica, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}