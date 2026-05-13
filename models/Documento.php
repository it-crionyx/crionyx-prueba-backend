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
    // Usamos los nombres reales: id_documento y aplica_retencion
    // Incluimos valor_base como 0 o NULL si no lo estamos calculando aún
    $sql = "INSERT INTO documentos_procesados (id_documento, aplica_retencion, valor_base) 
            VALUES (:id, :aplica, :base)";
    
    $stmt = $this->db->prepare($sql);
    
    // Vinculamos los valores reales
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':aplica', $aplica, PDO::PARAM_INT);
    $stmt->bindValue(':base', 0); // O el valor que desees guardar en valor_base
    
    return $stmt->execute();
  }
}