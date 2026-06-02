<?php
namespace Models;

use PDO;

class DocumentoModel
{
    private PDO $db;

    // Recibe la conexión PDO mediante Inyección de Dependencias
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Obtiene todos los documentos cruzando tablas y calculando retenciones dinámicamente
    public function obtenerConRetenciones(): array
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
        $documentos = $stmt->fetchAll();

        $resultado = [];
        foreach ($documentos as $doc) {
            // Regla de Negocio: Valor Base ($) = Base UVT * VALOR_UVT (actual)
            $basePesos = (float)$doc['base_uvt'] * (float)$doc['valor_uvt_sistema'];
            
            // Regla de Negocio: Si el valor_total es mayor al Valor Base ($), aplica retención
            $aplicaRetencion = (float)$doc['valor_total'] > $basePesos;

            $resultado[] = [
                'id_documento'     => (int)$doc['id_documento'],
                'nombre_proveedor' => $doc['nombre_proveedor'],
                'tipo_retencion'   => $doc['tipo_retencion'],
                'valor_total'      => (float)$doc['valor_total'],
                'base_pesos'       => $basePesos,
                'aplica_retencion' => $aplicaRetencion,
                'fecha'            => $doc['fecha']
            ];
        }

        return $resultado;
    }

    // Busca un documento específico por su ID usando una consulta preparada
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT id, valor_total, id_proveedor FROM documentos WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
}