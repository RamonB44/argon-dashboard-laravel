@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Perfil'])
<div class="container-fluid">
    <div class="row justify-content-center">

        <div class="col-md-10">
            @if (\Session::has('success'))
                    <div id="success-alert" class="alert alert-success text-center w-100">
                        <p>{!! \Session::get('success') !!}</p>
                </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-start">
                    <h2>Editar Usuario</h2>

                    {{-- <p class="login-box-msg">{{ __('adminlte::adminlte.register_message') }}</p> --}}
                </div>
                <form action="{{ route('users.profile') }}" method="post">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <small class="offset-md-2"><strong>Datos de usuario</strong></small>
                        <div class="row">
                            <div class="offset-md-2  col-md-4">
                                <small>Nombres Completos</small>
                                @if ($errors->has('name'))
                                    <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                @endif
                                <input type="text" class="form-control" name="name" id="name" {{ $errors->has('name') ? 'is-invalid' : '' }} value="{{ Auth::user()->name }}" autofocus>
                            </div>
                            <div class="col-md-4    ">
                                <small>Usuario</small>
                                @if ($errors->has('email'))
                                    <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                @endif
                                <input type="email" name="email" id="email" class="form-control" {{ $errors->has('email') ? 'is-invalid' : '' }} value="{{ Auth::user()->email }}" autofocus>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-2 col-md-4">
                                <small><strong class="text-info">Para no modificar, dejar en blanco la </strong>Contraseña</small>
                                @if ($errors->has('password'))
                                    {{-- <div class="invalid-feedback"> --}}
                                        <small class="text-danger"><strong>{{ $errors->first('password') }}</strong></small>
                                    {{-- </div> --}}
                                @endif
                                <input type="password" name="password" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <small>Confirmar Contraseña</small>
                                @if ($errors->has('password_confirmation'))
                                    {{-- <div class="invalid-feedback"> --}}
                                        <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                    {{-- </div> --}}
                                @endif
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" >
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success mr-2">Guardar</button>
                            <a class="btn btn-primary" href="{{ route('home') }}">Regresar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                $("#success-alert").slideUp(500);
            });
        });
    </script>
@endsection
