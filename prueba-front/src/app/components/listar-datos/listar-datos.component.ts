import { Component, inject, OnInit } from '@angular/core';
import { RespuestaService } from '../../service/respuesta.services';
import { CommonModule } from '@angular/common';
import Swal from 'sweetalert2';
import { TablaDocumentosComponent } from '../tabla-documentos/tabla-documentos.component';
import { InsertarDatosComponent } from '../insertar-datos/insertar-datos.component';



@Component({
  selector: 'app-padre',
  standalone: true,
  imports: [CommonModule, TablaDocumentosComponent,InsertarDatosComponent],
  templateUrl: './listar-datos.component.html',
  styleUrl: './listar-datos.component.css'
})
export class ListarUsuariosComponent implements OnInit {
  private readonly userService = inject(RespuestaService);
  listarDatos: any[] = [];

  
  ngOnInit(): void {
    this.consultarDatos();
    
  }

  consultarDatos(): void {
    this.userService.getdatos().subscribe(response => {
      if (response && response.respuestaDatos) {
        this.listarDatos = response.respuestaDatos;
        Swal.fire({
          title: response.message,
          icon: 'success',
          confirmButtonText: 'Aceptar'
        });

      } else {
        Swal.fire({
          title: 'Error',
          text: 'Respuesta inesperada del servidor',
          icon: 'warning',
        });
      }
    });
  }
 guardarNuevoDocumento(datosFormulario: any): void {
    this.userService.procesarDocumento(datosFormulario).subscribe({
      next: (response) => {
        if (response && response.ok) {
          Swal.fire({
            title: '¡Éxito!',
            text: response.message || 'Registro insertado correctamente',
            icon: 'success',
            confirmButtonText: 'Aceptar'
          });
          this.consultarDatos(); // Recarga la tabla de inmediato con el nuevo registro
        }
      },
      error: (err) => {
        Swal.fire({
          title: 'Error al guardar',
          text: err.error?.message || 'No se pudo conectar con el servidor',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  }

}