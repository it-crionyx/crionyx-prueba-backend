import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-insertar-datos',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './insertar-datos.component.html',
  styleUrl: './insertar-datos.component.css'
})
export class InsertarDatosComponent {
  // Emite el nuevo registro al componente padre para que lo guarde en la BD
  @Output() guardar = new EventEmitter<any>();

  // Objeto enlazado a los inputs del HTML
  nuevoDocumento = {
    id_documento: null,
    aplica_retencion: null,
    valor_base: ''
  };

  enviarFormulario(): void {
    // Validar que todos los campos contengan información
    if (!this.nuevoDocumento.id_documento || !this.nuevoDocumento.aplica_retencion || !this.nuevoDocumento.valor_base  ) {
      return;
    }

    // Enviamos una copia de los datos al componente Padre
    this.guardar.emit({ ...this.nuevoDocumento });
    this.limpiarFormulario();
  }

  limpiarFormulario(): void {
    this.nuevoDocumento = {
      id_documento: null,
      aplica_retencion: null,
      valor_base: ''
    };
  }
}
