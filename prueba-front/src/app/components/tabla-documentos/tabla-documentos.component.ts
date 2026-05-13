import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';

interface Documento {
  id: number;
  proveedor: string;
  valor_total: number;
  fecha: string;
  base_uvt: number;
  valor_uvt: number;
  valor_base_pesos?: number;
  aplica_retencion?: boolean;
}

@Component({
  selector: 'app-tabla-documentos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './tabla-documentos.component.html',
  styleUrls: ['./tabla-documentos.component.css']  // Corregido
})
export class TablaDocumentosComponent {
  @Input() listarDatos: Documento[] = [];
  @Output() usuarioEliminado = new EventEmitter<number>();
  @Output() editarUsuario = new EventEmitter<Documento>();

  // Métodos para emitir eventos desde la plantilla
  eliminarDocumento(id: number): void {
    this.usuarioEliminado.emit(id);
  }

  editarDocumento(doc: Documento): void {
    this.editarUsuario.emit(doc);
  }

}