<?php
namespace Model;

use PDO;
use Exception;

class ProcessModel
{
    private PDO $db;

    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function processBatch(array $documents): array
    {
        $this->db->beginTransaction();

        try {
            
            $sql = "INSERT INTO documentos_procesados (id_documento, aplica_retencion, valor_base) 
                    VALUES (:id_documento, :aplica_retencion, :valor_base)";
            $stmt = $this->db->prepare($sql);

            foreach ($documents as $document) {
                // Validación para evitar duplicados o IDs inexistentes en el lote
                if ($this->wasProcessed($document['id_documento'])) {
                    throw new Exception("El documento con ID {$document['id_documento']} ya fue procesado.");
                }

                // Ejecución directa mapeando los valores tipados
                $stmt->execute([
                    ':id_documento'     => $document['id_documento'],
                    ':aplica_retencion' => $document['aplica_retencion'],
                    ':valor_base'       => $document['valor_base']
                ]);
            }

            // Si todo el lote es correcto, se guardan los cambios de forma definitiva
            $this->db->commit();
            return ['procesados' => count($documents)];

        } catch (Exception $e) {
            // Si tan solo uno falla, se revierte absolutamente todo el lote (Consistencia Atómica)
            $this->db->rollBack();
            throw $e;
        }
    }

    
    private function wasProcessed(int $documentId): bool
    {
        $sql = "SELECT COUNT(*) FROM documentos_procesados WHERE id_documento = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        
        return (int)$stmt->fetchColumn() > 0;
    }
}