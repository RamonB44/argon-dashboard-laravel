@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Crear grupos de trabajo'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">
                        <form action="{{ route('group.create') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <small><strong>Nombre del Grupo</strong></small>
                                <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Ingresar nombre de permiso">
                            </div>
                            {{-- Permission Tables --}}
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerenciaProduccion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRGerenciaGeneral" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Registro de asistencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="registerIngreso" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewAsistencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="registerSalida" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Plataforma de Validaciones</td>
                                            <td>
                                                {{-- <input type="checkbox" name="permission[]" id="permission" value="registerIngreso" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger"> --}}
                                                -
                                            </td>
                                            <td>
                                                -
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewValidaciones" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                -
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                            {{-- Permission Tables Breads --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewMant" data-toggle="toggle" data-size="sm">
                                            </th>
                                            <th>Crear</th>
                                            <th>Actualizar</th>
                                            <th>Ver</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    </thead>
                                    <small><strong>Permisos en Mantenimientos</strong></small>
                                    <tbody class="text-center">
                                        <tr>
                                            <td>Sedes</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createSedes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateSedes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewSedes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteSedes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gerencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteGerencia" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Area</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createArea" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateArea" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewArea" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteArea" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Funciones</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createFuncion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateFuncion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewFuncion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteFuncion" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Procesos</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createProcesos" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateProcesos" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewProcesos" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteProcesos" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewAux" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteTreg" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tipo de Documento</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteTdoc" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reg. Suplencia</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteRsuple" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGEmployes" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteEmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Gestion de procesos por empleado</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteProcessEmploye" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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

                            {{-- Permission Gestion de Asistencias  --}}
                            <div class="table">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 15%">
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGHA" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteHA" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHorarios" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteHorarios" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewReportes" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteRAssistance" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reporte Empleados</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteREmployes" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="viewGUsuarios" data-toggle="toggle" data-size="sm">
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
                                                <input type="checkbox" name="permission[]" id="permission" value="createPermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updatePermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewPermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deletePermission" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Usuarios</td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="createUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="updateUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="viewUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="permission[]" id="permission" value="deleteUsers" data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="danger">
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            <div class="panel-footer pull-right">
                                @section('submit-buttons')
                                    <button type="submit" class="btn btn-primary save">Guardar</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-warning">Regresar</a>
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
