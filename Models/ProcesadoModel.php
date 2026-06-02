<?php
namespace Models;

use PDO;
use Exception;

class ProcesadoModel
{
    private PDO $db;

    // Recibe la conexión PDO mediante Inyección de Dependencias
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Inserta un lote de documentos utilizando una transacción atómica
    public function insertarLote(array $listaDocumentos): array
    {
        $this->db->beginTransaction();

        try {
            // Preparamos la consulta una sola vez afuera del bucle (optimización de rendimiento)
            $sql = "INSERT INTO documentos_procesados (id_documento, aplica_retencion, valor_base) 
                    VALUES (:id_documento, :aplica_retencion, :valor_base)";
            $stmt = $this->db->prepare($sql);

            foreach ($listaDocumentos as $doc) {
                // Validación para evitar duplicados o IDs inexistentes en el lote
                if ($this->yaFueProcesado($doc['id_documento'])) {
                    throw new Exception("El documento con ID {$doc['id_documento']} ya fue procesado.");
                }

                // Ejecución directa mapeando los valores tipados
                $stmt->execute([
                    ':id_documento'     => $doc['id_documento'],
                    ':aplica_retencion' => $doc['aplica_retencion'],
                    ':valor_base'       => $doc['valor_base']
                ]);
            }

            // Si todo el lote es correcto, se guardan los cambios de forma definitiva
            $this->db->commit();
            return ['procesados' => count($listaDocumentos)];

        } catch (Exception $e) {
            // Si tan solo uno falla, se revierte absolutamente todo el lote (Consistencia Atómica)
            $this->db->rollBack();
            throw $e;
        }
    }

    // Verifica si un documento ya fue registrado previamente en el histórico
    private function yaFueProcesado(int $idDocumento): bool
    {
        $sql = "SELECT COUNT(*) FROM documentos_procesados WHERE id_documento = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idDocumento]);
        
        return (int)$stmt->fetchColumn() > 0;
    }
}