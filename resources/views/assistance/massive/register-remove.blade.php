@extends('adminlte::page')

@section('title', 'Consultas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex bd-highlight">
                        <div class="p-2 bd-highlight">
                            <br><h2 class="text-inline text-bold">Asistencias Masivas</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formRegister" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4">
                                <small>Descripcion</small>
                                <select name="description" id="description_r" class="form-control">
                                    <option value="ASISTENCIA">ASISTENCIA</option>
                                    <option value="LICENCIA">LICENCIA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" id="d_since_at_r" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" id="d_until_at_r"  value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                                <small>Desde la hora [24 hrs]</small>
                                <input type="time" name="h_since_at" id="h_since_at_r" value="08:00" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <small>Hasta la hora [24 hrs]</small>
                                <input type="time" name="h_until_at" id="h_until_at_r" value="17:00" class="form-control">
                            </div>
                        </div>
                        <div class="table row">
                            <div class="col-md-6">
                                <small>Empleados Seleecionados</small>
                                <table id="manageTableAssit" class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>Codigo</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>N° Registros</th>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <small>Registros</small>
                                <table id="manageTableAssit" class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>Desde/Hasta</th>
                                        <th>Tipo</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('plugins.Datatables', true)
@section('plugins.DataRangePickerJs', true)
@section('js')
<script>
</script>
@endsection
