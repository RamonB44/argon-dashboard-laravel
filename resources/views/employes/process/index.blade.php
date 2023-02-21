@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestion de Trabajadores'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" data-step="1"
                        data-intro="Modulo de procesos por empleados , aqui podremos gestionar los procesos del personal">
                        <div class="row">
                            <div class="col-md text-center">
                                <div class="mr-auto p-2 bd-highlight ">
                                    <h3>Gestion de Trabajadores</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row bd-highlight">
                            {{-- <div class="col-md bd-highlight mb-2">
                            <a href="javascript:changeProcedure()" class="btn btn-primary btn-sm btn-block disabled">Cambiar procesos</a>
                        </div>
                        <div class="col-md bd-highlight mb-2">
                            <a data-step="3"
                            data-intro="Previamente seleccionado al personal , hacer click en este boton para añadir nuevos procesos a el personal"
                            href="javascript:register()" class="btn btn-primary btn-sm btn-block disabled">Añadir/Remover Procesos</a>
                        </div> --}}
                            <div class="col-md bd-hightlight mb-2">
                                <select name="option-value" id="option-value" class="form-control">
                                    <option value="" selected disabled hidden>Seleccionar Importacion</option>
                                    <option value="procesos">Importar Procesos</option>
                                    <option value="funciones">Importar Funciones</option>
                                    <option value="dir_ind">Importar Costos</option>
                                    <option value="type">Importar Tipo Trabajador</option>
                                    <option value="turno">Importar Turno</option>
                                    <option value="remuneracion">Importar Remuneracion</option>
                                    <option value="tipo_empleado">Importar Tipo de Empleado</option>
                                </select>
                            </div>
                            <div class="col-md bd-highlight">
                                {{-- <small>Importacion <strong></strong></small> --}}
                                <form class="mb-1" action="{{ route('manageprocess.import') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="option" id="option" value="" required>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="file_xlsx" id="file_xlsx" class="custom-file-input"
                                                required>
                                            <label class="custom-file-label" for="file_xlsx">Elegir Excel</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-success" id="">Importar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (\Session::has('success'))
                            <div id="success-alert" class="alert alert-success text-center w-100">
                                <p>{!! \Session::get('success') !!}</p>
                            </div>
                        @endif
                        <div class="row">
                            {{-- <small><strong>Lista de Empleados</strong></small> --}}
                            {{-- <div class="col-md-12"> --}}
                            <div class="table" style="overflow-x: auto">
                                <table id="manageTable" class="table table-bordered" style="width: 100%" data-step="2"
                                    data-intro="Lista del personal, aqui podras seleccionar al personal para añadir sus nuevos procesos.">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Codigo</th>
                                            <!--<th>Codigo de validacion</th>-->
                                            <th>Documento N°</th>
                                            <th>Nombres y Apellidos</th>
                                            <th>Area</th>
                                            <th>Funcion</th>
                                            <th>Proceso</th>
                                            <th>Sede</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="createModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Añadir/Remover Registros</h4>
                </div>
                <form id="formRegister" action="{{ route('manageprocess.massiveReg') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="offset-md-3 col-md-2">
                                <small>Tipo</small>
                                <select name="tipo" id="type_r" class="form-control">
                                    <option value="1">REGISTRAR</option>
                                    <option value="2">REMOVER</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Descripcion</small>
                                <select name="description" id="description_r" class="form-control">
                                    @foreach (App\Models\Procesos::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_until_at" id="d_until_at_r" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <hr>
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
                                <small>Registros de <strong id="employe"></strong></small>
                                <table id="manageTableRegister" class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>Proceso</th>
                                        <th>Registrado el</th>
                                        <th>Finalizo el</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
                <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
@endsection


@section('css')
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        var manageTable = null;
        var url = "{{ url('/') }}";
        $(document).ready(function() {
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
                $("#success-alert").slideUp(500);
            });

            if (RegExp('multipage', 'gi').test(window.location.search)) {
                introJs().setOption('doneLabel', 'Siguiente Pagina').start().oncomplete(function() {
                    window.location.href = url + '/manageassistance?multipage=true';
                });
            }
            //Timepicker
            // $('#timepicker').datetimepicker({
            //   format: 'LT'
            // });


            manageTable = $('#manageTable').DataTable({
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad"
                    }
                },
                orderCellsTop: true,
                fixedHeader: true,
                bLengthChange: false,
                bVisible: false,
                columnDefs: [{
                    visible: false,
                    targets: null,
                    defaultContent: '',
                    orderable: false,
                    className: 'select-checkbox'
                }],

                ajax: {
                    type: 'get',
                    url: '{{ route('employes.getTable') }}',
                    beforeSend: function() {
                        $('.progress').show();
                    },
                    complete: function() {
                        // $('.progress').hide();
                    },
                },
            });
        });

        var manageTableAssit = null;
        var manageTableRegister = null;

        function register() {
            // var contador = 0;
            // console.log('registro')
            ids = $.map(manageTable.rows('.selected').data(), function(item) {
                return item[1];
            });

            if (ids.length > 0) {
                //open model

                if (manageTableAssit) {
                    $('#manageTableAssit').DataTable().destroy();
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                manageTableAssit = $('#manageTableAssit').DataTable({
                    "language": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "No se encontraron resultados",
                        "sEmptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        },
                        "buttons": {
                            "copy": "Copiar",
                            "colvis": "Visibilidad"
                        }
                    },
                    orderCellsTop: true,
                    fixedHeader: true,
                    bLengthChange: false,
                    bVisible: false,
                    pageLength: 4,
                    ajax: {
                        type: 'post',
                        url: '{{ route('employes.process') }}',
                        data: {
                            ids
                        }
                    },
                });

                $('#manageTableAssit tbody').on('click', 'tr', function() {
                    var code = $(this.children[1]).text();
                    $('#employe').text("[" + $(this.children[1]).text() + "] " + $(this.children[2]).text());
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                        if (manageTableRegister) {
                            $('#manageTableRegister').DataTable().destroy();
                            $('#manageTableRegister tbody').children().remove();
                        }
                    } else {
                        manageTableAssit.$('tr.selected').removeClass('selected');
                        $(this).addClass('selected');
                        loadRegister(code);
                    }

                });

                $('#createModal').modal('show');

            } else {
                alert('Selecciona algun empleado');
                return;
            }
        }

        function loadRegister(codigo) {

            if (manageTableRegister) {
                $('#manageTableRegister').DataTable().destroy();
            }

            manageTableRegister = $('#manageTableRegister').DataTable({
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad"
                    }
                },
                orderCellsTop: true,
                fixedHeader: true,
                bLengthChange: false,
                bVisible: false,
                pageLength: 4,
                ajax: {
                    type: 'get',
                    url: url + '/employes/manageprocess/getregister/' + codigo,
                    // data: {ids}
                },
            });
        }

        $('#formRegister').submit(function(e) {
            e.preventDefault();
            var datos = {};
            // datos['d_since_at'] = $('#d_since_at_r').val();
            // datos['d_until_at'] = $('#d_until_at_r').val();
            // datos['h_until_at'] = $('#h_until_at_r').val();
            // datos['h_since_at'] = $('#h_since_at_r').val();
            // datos['type'] = $('#type_r').val();
            // datos['description'] = $('#description_r').val();
            var datos = {};
            $("form#formRegister :input").each(function(e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(this).attr('name')] = $(this).val();

            });
            // datos['type'] = $('#type_r').val();
            datos['ids'] = ids;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            Swal.fire({
                title: 'Esta seguro?',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si , Modidicalos!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: datos,
                        success: function(response) {
                            console.log(response);
                            showMessageBox(response);
                            manageTableRegister.ajax.reload(null, false);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            })
        });

        function showMessageBox(response) {
            Swal.fire({
                // position: 'top-end',
                icon: response.icon,
                title: response.title,
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            })
        }

        function changeProcedure() {
            var options = null;
            $.ajax({
                url: "{{ route('procesos.getData') }}",
                method: "get",
                dataType: "json",
                success: function(response) {
                    Swal.mixin({
                        input: 'select',
                        inputOptions: response,
                        inputPlaceholder: 'Empleados con el proceso de?',
                        showCancelButton: true,
                        confirmButtonText: 'Siguente',
                        progressSteps: ['1', '2'],
                        inputValidator: function(value) {
                            return new Promise(function(resolve, reject) {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    resolve('Necesitas seleccionar un proceso');
                                }
                            });
                        }
                    }).queue([{
                            title: 'Proceso actual de los empleados',
                            text: 'Selecciona el actual proceso'
                        },
                        {
                            title: 'Nuevo proceso de los empleados',
                            text: 'Selecciona el nuevo proceso'
                        },
                    ]).then((result) => {
                        if (result.value) {
                            const answers = JSON.stringify(result.value);
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: "{{ route('manageprocess.changeMasive') }}",
                                method: "post",
                                dataType: "json",
                                data: {
                                    "id": answers
                                },
                                success: function(response) {
                                    showMessageBox(response);
                                },
                                error: function(response) {
                                    console.log(response)
                                }
                            })
                        }
                    })
                },
                error: function(ex) {
                    console.log(ex);
                }
            });
        }

        $('#option-value').on('change', function() {
            // .on('change',(e)=>{
            $('#option').val($(this).val());
            // });
        });
    </script>
@endsection
