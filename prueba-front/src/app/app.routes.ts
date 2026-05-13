import { Routes } from '@angular/router';
import { ListarUsuariosComponent } from './components/listar-datos/listar-datos.component';

export const routes: Routes = [

    
{ path: 'path', component: ListarUsuariosComponent },
{ path: '**', component: ListarUsuariosComponent },
];



