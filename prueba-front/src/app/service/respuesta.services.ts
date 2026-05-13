import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class RespuestaService {
    private readonly apiUrl = 'http://localhost:3000/api/documentos';

    constructor(private http: HttpClient) { }

    getdatos(): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/consultar-datos`);
    }
    procesarDocumento(datos: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/procesar`, datos);
  }
}