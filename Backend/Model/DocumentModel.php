<?php
namespace Model;

use PDO;

class DocumentModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getDocumentsWithWithholdings(): array
    {
        $sql = "
            SELECT
                d.id               AS id_documento,
                p.nombre           AS nombre_proveedor,
                r.nombre           AS tipo_retencion,
                r.base_uvt         AS base_uvt,
                d.valor_total      AS valor_total,
                v.valor            AS valor_uvt_sistema,
                d.fecha            AS fecha
            FROM documentos d
            INNER JOIN proveedores p ON d.id_proveedor = p.id
            INNER JOIN retenciones r ON p.id_retencion_aplicable = r.id
            INNER JOIN variables_sistema v ON v.clave = 'VALOR_UVT'
            ORDER BY d.id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $documents = $stmt->fetchAll();

        $result = [];
        foreach ($documents as $document) {
            // Regla de Negocio: Valor Base ($) = Base UVT * VALOR_UVT (actual)
            $basePesos = (float)$document['base_uvt'] * (float)$document['valor_uvt_sistema'];
            
            // Regla de Negocio: Si el valor_total es mayor al Valor Base ($), aplica retención
            $appliesWithholding = (float)$document['valor_total'] > $basePesos;

            $result[] = [
                'id_documento'     => (int)$document['id_documento'],
                'nombre_proveedor' => $document['nombre_proveedor'],
                'tipo_retencion'   => $document['tipo_retencion'],
                'base_uvt'         => (float)$document['base_uvt'],
                'valor_total'      => (float)$document['valor_total'],
                'base_pesos'       => $basePesos,
                'aplica_retencion' => $appliesWithholding,
                'fecha'            => $document['fecha']
            ];
        }

        return $result;
    }

    
    public function getById(int $id): array|false
    {
        $sql = "SELECT id, valor_total, id_proveedor FROM documentos WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
}