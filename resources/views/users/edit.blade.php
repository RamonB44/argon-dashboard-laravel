@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar Usuarios'])

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>Editar Usuario</h2>
                        {{-- <p class="login-box-msg">{{ __('adminlte::adminlte.register_message') }}</p> --}}
                    </div>
                    <form action="{{ route('users.update', ['id' => $user->id]) }}" method="post">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <small class="offset-md-2"><strong>Datos de usuario</strong></small>
                            <div class="row">
                                <div class="offset-md-2  col-md-4">
                                    <small>Nombres Completos</small>
                                    @if ($errors->has('name'))
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                    @endif
                                    <input type="text" class="form-control" name="name" id="name"
                                        {{ $errors->has('name') ? 'is-invalid' : '' }} value="{{ $user->name }}"
                                        autofocus>
                                </div>
                                <div class="col-md-4">
                                    <small>Rol</small>
                                    <select name="group" id="group" class="form-control">
                                        @foreach ($group as $item)
                                            <option <?php if ($item->id == $user->user_group->first()->id) {
                                                echo 'selected';
                                            } ?> value="{{ $item->id }}">{{ $item->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-md-2 col-md-4    ">
                                    <small>Usuario</small>
                                    @if ($errors->has('email'))
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                    @endif
                                    <input type="email" name="email" id="email" class="form-control"
                                        {{ $errors->has('email') ? 'is-invalid' : '' }} value="{{ $user->email }}"
                                        autofocus>
                                </div>
                                <div class="col-md-4    ">
                                    <small>Sedes</small>
                                    @if ($errors->has('sedes'))
                                        <strong class="text-danger">{{ $errors->first('sedes') }}</strong>
                                    @endif
                                    <select name="sedes[]" id="sedes" class="form-control" multiple="multiple">
                                        @foreach ($sedes as $item)
                                            <option <?php if (in_array($item->id, json_decode($user->user_group->first()->pivot->sedes))) {
                                                echo 'selected';
                                            } ?> value="{{ $item->id }}">{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-md-2 col-md-4">
                                    <small><strong class="text-info">Para no modificar, dejar en blanco la
                                        </strong>Contraseña</small>
                                    @if ($errors->has('password'))
                                        {{-- <div class="invalid-feedback"> --}}
                                        <small
                                            class="text-danger"><strong>{{ $errors->first('password') }}</strong></small>
                                        {{-- </div> --}}
                                    @endif
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <small>Confirmar Contraseña</small>
                                    @if ($errors->has('password_confirmation'))
                                        {{-- <div class="invalid-feedback"> --}}
                                        <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                        {{-- </div> --}}
                                    @endif
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
                                </div>

                            </div>
                            <small><strong>Configuracion : Graficos</strong></small>
                            <div class="row">
                                <div class="col-md-11">
                                    <small>Mostrar areas</small>
                                    <select name="areas[]" id="areas" class="form-control" multiple="multiple">
                                        @foreach ($areas as $item)
                                            <option <?php if (in_array($item->id, json_decode($user->user_group->first()->pivot->show_areas))) {
                                                echo 'selected';
                                            } ?> selected value="{{ $item->id }}">
                                                {{ $item->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-11">
                                    <small>Mostrar Tipo de registro</small>
                                    <select name="treg[]" id="treg" class="form-control" multiple="multiple">
                                        @foreach ($treg as $item)
                                            <option <?php if (in_array($item->id, json_decode($user->user_group->first()->pivot->show_aux_treg))) {
                                                echo 'selected';
                                            } ?> value="{{ $item->id }}">
                                                {{ $item->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success mr-2">Guardar</button>
                                <a class="btn btn-primary" href="{{ route('users.index') }}">Regresar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css" rel="stylesheet" />
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>
        // var manageTable = null;
        var url = "{{ url('/') }}";
        $(document).ready(function() {
            $('#areas').select2({
                placeholder: "Seleccione las areas a mostrar",
            });

            $('#treg').select2({
                placeholder: "Seleccione los tipos de registro a mostrar",
            });

            $('#sedes').select2({
                placeholder: "Seleccione la sedes a las que pertenece",
            })
        });
    </script>
@endsection
