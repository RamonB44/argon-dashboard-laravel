@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar grupos de trabajo'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">
                        @php
                        $serialize_permission = unserialize($data->permission);
                        @endphp
                        <form action="{{ route('group.update',["id" => $data->id]) }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <small><strong>Nombre del Grupo</strong></small>
                                <input type="text" class="form-control" id="group_name" name="group_name" value="{{ $data->group_name  }}" placeholder="Ingresar nombre de permiso">
                            </div>
                            {{-- Permission Tables Breads --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos Especiales</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Gerencia</td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewRGerencia', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gerencia Produccion</td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerenciaProduccion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewRGerenciaProduccion', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gerencia General</td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerenciaGeneral" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewRGerenciaGeneral', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gerencia Recursos</td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerenciaRecursos" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewRGerenciaRecursos', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Registro de asistencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="registerIngreso" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('registerIngreso', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewAsistencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewAsistencia', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="registerSalida" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('registerSalida', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Plataforma de Validacion</td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewValidaciones" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewValidaciones', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Tables Auxiliar  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewAux" data-toggle="toggle" data-size="sm"<?php if($serialize_permission) {
                                                    if(in_array('viewAux', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Auxiliares</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Tipo de Registro</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createTreg', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateTreg', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewTreg', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteTreg', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tipo de Documento</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createTdoc', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateTdoc', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewTdoc', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteTdoc', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reg. Suplencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createRsuple', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateRsuple', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewRsuple', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteRsuple', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Gestion de Empleados  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGEmployes" data-toggle="toggle" data-size="sm"<?php if($serialize_permission) {
                                                    if(in_array('viewGEmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Gestion de empleados</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Empleados</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createEmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateEmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewEmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteEmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gestion de procesos por empleado</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('createProcessEmploye', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('updateProcessEmploye', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewProcessEmploye', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('deleteProcessEmploye', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Gestion de Jornales/Destajeros</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createJD" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateJD" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewJD" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteJDe" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr> --}}
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Gestion de Asistencias/Horarios  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGHA" data-toggle="toggle" data-size="sm"<?php if($serialize_permission) {
                                                    if(in_array('viewGHA', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Gestion de asistencias</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Asistencias por Empleado/Masivas</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createHA', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateHA', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewHA', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteHA', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Gestion de Horarios  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHorarios" data-toggle="toggle" data-size="sm"<?php if($serialize_permission) {
                                                    if(in_array('viewHorarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Gestion de horarios</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Horarios por Areas/Masivas</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createHorarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateHorarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewHorarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteHorarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Gestion de Reportes  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewReportes" data-toggle="toggle" data-size="sm" <?php if($serialize_permission) {
                                                    if(in_array('viewReportes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Gestion de Reportes</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Reporte Asistencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('createRAssistance', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('updateRAssistance', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewRAssistance', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteRAssistance', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reporte Empleados</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createREmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateREmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewREmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteREmployes', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            {{-- Permission Gestion de Usuarios  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGUsuarios" data-toggle="toggle" data-size="sm"<?php if($serialize_permission) {
                                                    if(in_array('viewGUsuarios', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Gestion de Usuarios</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Permisos</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createPermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createPermission', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updatePermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('updatePermission', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewPermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('viewPermission', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deletePermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger" <?php if($serialize_permission) {
                                                    if(in_array('deletePermission', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Usuarios</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('createUsers', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('updateUsers', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('viewUsers', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"<?php if($serialize_permission) {
                                                    if(in_array('deleteUsers', $serialize_permission)) { echo "checked"; }
                                                    } ?>>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            <div class="panel-footer pull-right">
                                @section('submit-buttons')
                                    <button type="submit" class="btn btn-primary save">Guardar</button>
                                    <a href="{{ redirect()->back() }}" class="btn btn-warning">Regresar</a>
                                @stop
                                @yield('submit-buttons')
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
@endsection
