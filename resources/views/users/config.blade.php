@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Configuracion de Usuario'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (\Session::has('success'))
                    <div id="success-alert" class="alert alert-success text-center w-100">
                        <p>{!! \Session::get('success') !!}</p>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>Configuraciones</h2>
                        {{-- <p class="login-box-msg">{{ __('adminlte::adminlte.register_message') }}</p> --}}
                    </div>
                    <form action="{{ route('users.config') }}" method="post">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <small><strong>Configuracion : Graficos</strong></small>
                            <div class="row">
                                <div class="col-md-11">
                                    {{-- when do click in checkbox and change state of component to enabled also enabled select viceversa too --}}
                                    <small><input type="checkbox" id="areas-c" checked data-toggle="toggle"
                                            data-on="Enabled" data-off="Disabled"data-size="sm" data-onstyle="success"
                                            data-offstyle="danger"></small>
                                    <select name="areas[]" id="areas" class="form-control" multiple="multiple">
                                        @foreach ($areas as $item)
                                            <option <?php if (in_array($item->id, json_decode(Auth::user()->user_group->first()->pivot->show_areas))) {
                                                echo 'selected';
                                            } ?> value="{{ $item->id }}">{{ $item->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-11">
                                    {{-- when do click in checkbox and change state of component to enabled also enabled select viceversa too --}}
                                    <small><input type="checkbox" id="treg-c" checked data-toggle="toggle"
                                            data-on="Enabled" data-off="Disabled"data-size="sm" data-onstyle="success"
                                            data-offstyle="danger"> </small>
                                    <select name="treg[]" id="treg" class="form-control" multiple="multiple">
                                        @foreach ($treg as $item)
                                            <option <?php if (in_array($item->id, json_decode(Auth::user()->user_group->first()->pivot->show_aux_treg))) {
                                                echo 'selected';
                                            } ?> value="{{ $item->id }}">{{ $item->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                                // dd(Auth::user()->user_group->first()->pivot_id_group != 23)
                            @endphp
                            <div class="row" <?php if (Auth::user()->user_group->first()->id != 23 && Auth::user()->user_group->first()->id != 19) {
                                echo 'hidden';
                            } ?>>
                                <div class="custom-control custom-switch mt-2 d-flex justify-content-center">
                                    <input type="checkbox" class="custom-control-input" id="funct_c" <?php if (count(json_decode(Auth::user()->user_group->first()->pivot->show_function)) > 0) {
                                        echo 'checked';
                                    } ?>>
                                    <label class="custom-control-label" for="funct_c">Funciones</label>
                                </div>
                                <div class="col-md-11">
                                    {{-- when do click in checkbox and change state of component to enabled also enabled select viceversa too --}}

                                    <input type="hidden" name="funct">
                                    <select name="funct[]" id="funct" class="form-control" multiple="multiple"
                                        <?php if (!count(json_decode(Auth::user()->user_group->first()->pivot->show_function)) > 0) {
                                            echo 'disabled';
                                        } ?>>
                                        @foreach (\App\Models\Area::whereIn('id', json_decode(Auth::user()->user_group->first()->pivot->show_areas))->get() as $value)
                                            <optgroup label="{{ $value->area }}">
                                                <!-- HTML for the `children` -->
                                                @foreach (App\Models\Funcion::where('id_area', $value->id)->get() as $item)
                                                    <option <?php if (in_array($item->id, json_decode(Auth::user()->user_group->first()->pivot->show_function))) {
                                                        echo 'selected';
                                                    } ?> value="{{ $item->id }}">
                                                        {{ $item->description }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
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


@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
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

            $('#funct').select2({
                placeholder: "Seleccione los tipos de registro a mostrar",
            });

            $("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
                $("#success-alert").slideUp(500);
            });

            $('#funct_c').on("click", function() {
                // console.log("O")
                if ($('#funct_c').is(':checked')) {
                    // $('#funct').prop('disabled',true);
                    $('#funct').prop("disabled", false);
                    $("#funct").find('option').prop("selected", true);
                    $("#funct").trigger("change");
                } else {
                    // $('#codeOrdni').val("0");
                    $('#funct').prop("disabled", true);
                    $("#funct").find('option').prop("selected", false);
                    $("#funct").trigger("change");
                }
            });

        });
    </script>
@endsection
