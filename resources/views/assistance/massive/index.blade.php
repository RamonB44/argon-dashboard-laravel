@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Asistencia Masiva'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" data-step="1"
                        data-intro="Modulo de gestion de asistencia masiva del personal , en este modulo podremos gestionar los registros de asistencia del personal de manera masiva.">
                        <div class="row">
                            <div class="col-md d-flex justify-content-start">
                                <br>
                                <h3 class="text-inline text-bold">Asistencias Masivas</h3>
                            </div>
                            <div class="col-md" data-step="2"
                                data-intro="filtrar al personal segun el area que seleccionemos.">
                                <small>Areas</small>
                                <select name="areas" id="areas" class="form-control">
                                    <option value="0" selected>Todas</option>
                                    @foreach ($areas as $item)
                                        <option value="{{ $item->id }}">{{ $item->area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md" data-step="3"
                                data-intro="añadir y remover registros de asistencia al personal que deseamos.">
                                <a class="btn btn-primary btn-lg h-100 w-100" href="javascript:register()">Añadir/Remover Registros</a>
                            </div>
                            {{-- <div class="p-2 bd-highlight">
                            <br>
                            <a class="btn btn-danger" href="javascript:remove()">Remover Registros</a>
                        </div> --}}
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md" data-step="4"
                                data-intro="importar de manera masiva desde las marcas generadas por el biometrico, archivo de importacion excel.">
                                <small>Importar Ingresos desde Marcas</small>
                                <form method="post" action="{{ route('assistance.import_marcas') }}"
                                    enctype="multipart/form-data">
                                    <div class="input-group">
                                        @csrf
                                        <input type="hidden" name="in_out" value="1">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_xlsx" id="file_xlsx">
                                            <label class="custom-file-label" for="file_xlsx">Elegir Excel</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-success" id="">Importar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md" data-step="5"
                                data-intro="importar de manera masiva desde otro formato mas sencillo y mejor, archivo de importacion excel.">
                                <small>Importar Salidas desde Marcas</small>
                                <form method="post" action="{{ route('assistance.import_marcas') }}"
                                    enctype="multipart/form-data">
                                    <div class="input-group">
                                        @csrf
                                        <input type="hidden" name="in_out" value="0">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_xlsx" id="file_xlsx">
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
                    <div class="table card-body">
                        <table id="manageTable" class="table table-bordered" data-step="6"
                            data-intro="la lista del personal, para añadir registros solo debemos seleccionar al personal que deseamos y luego hacer click en el boton de [Añadir/Remover Registros].">
                            <thead>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Documento</th>
                                <th>Nombres y Apellidos</th>
                                <th>Area</th>
                                <th>Proceso</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" id="createModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Añadir/Remover Registros</h4>
                </div>
                <form id="formRegister" action="{{ route('manageAssist.massiveReg') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <small>Descripcion</small>
                                <select name="description" id="description_r" class="form-control">
                                    @foreach (App\Models\Auxiliar\TypeReg::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" id="d_since_at_r" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" id="d_until_at_r" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <small>Tipo</small>
                                <select name="tipo" id="type_r" class="form-control">
                                    <option value="1">REGISTRAR</option>
                                    <option value="2">REMOVER</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Desde la hora [24 hrs]</small>
                                <input type="time" name="h_since_at" id="h_since_at_r" value="08:00"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <small>Hasta la hora [24 hrs]</small>
                                <input type="time" name="h_until_at" id="h_until_at_r" value="17:00"
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
                                        <th>Desde</th>
                                        <th>Hasta</th>
                                        <th>Tipo</th>
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
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
@endsection

@section('js')
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/moment.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        var url = "{{ url('/') }}";
        var manageTable = null;
        var ids = [];
        var introApp = null;
        $(document).ready(function() {

            if (RegExp('multipage', 'gi').test(window.location.search)) {

                introApp = introJs().setOption('doneLabel', 'Siguiente Pagina').start().oncomplete(function() {
                    window.location.href = url + '/managehours?multipage=true';
                });

                introApp.onafterchange(function(targetElement) {
                    console.log(targetElement.id);
                    switch (targetElement.id) {
                        case "manageTable":
                            // console.log("haaaaaaaaaaaa")
                            // $(".introjs-tooltipReferenceLayer > div").html("Jajaja");
                            // $('.introjs-tooltipReferenceLayer > div').css({"botton":'250px',"left":'450px',"top":'0px'});
                            // jQuery('.introjs-tooltip').css({
                            //         opacity: 1,
                            //         display: 'block',
                            //         botton: '250px',
                            //         left: '450px',
                            //     });
                            // targetElement.setOption('positionPrecedence', ['top', 'bottom', 'left', 'right'])
                            break;
                        case "createModal":
                            // $('.introjs-tooltip').css({"marginBotton":'250px',"marginLeft ":'450px'});
                            // $('#createModal').modal('show');
                            // if(targetElement.hasClass('introjsFloatingElement')){

                            //     // adjust the position of these elements
                            //     // jQuery('.introjs-tooltipReferenceLayer').offset({top : 120});
                            //     jQuery('.introjs-tooltip').css({
                            //         opacity: 1,
                            //         display: 'block',
                            //         left: '50%',
                            //         top: '50%',
                            //         'margin-left': '-186px',
                            //         'margin-top': '-91px'
                            //     });
                            //     // jQuery('.introjs-helperNumberLayer').css({
                            //     //     opacity: 1,
                            //     //     left: '-204px',
                            //     //     top: '-109px'
                            //     // });
                            // }
                            // introApp.setOption('positionPrecedence', ['top', 'bottom', 'left', 'right'])
                            // "ok"
                            // $('.introjs-tooltip').css({botton:'250px',left:'450px'});
                            break;
                    }
                });

                // $('#createModal').modal('show');
            }

            $('#manageTable thead tr').clone(true).appendTo('#manageTable thead');

            $('#manageTable thead tr:eq(1) th').each(function(i) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Buscar ' + title +
                    '" />');

                $('input', this).on('keyup change', function() {
                    if (manageTable.column(i).search() !== this.value) {
                        manageTable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });

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
                select: {
                    style: 'os',
                    selector: 'td'
                },
                ajax: {
                    type: 'get',
                    url: url + '/employes/getTableArea/' + $('#areas').val(),
                    beforeSend: function() {
                        $('.progress').show();
                    },
                    complete: function() {
                        // $('.progress').hide();
                    },
                },
            });
        });

        $('#areas').change(function() {
            $('#manageTable').DataTable().destroy();
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
                select: {
                    style: 'os',
                    selector: 'td'
                },
                ajax: {
                    type: 'get',
                    url: url + '/employes/getTableArea/' + $('#areas').val(),
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

            console.log(ids);
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
                        url: '{{ route('employes.group') }}',
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
                    url: url + '/manageassistance/getregister/' + codigo,
                    // data: {ids}
                },
            });
        }

        $('#formRegister').submit(function(e) {
            e.preventDefault();
            var datos = {};
            datos['d_since_at'] = $('#d_since_at_r').val();
            datos['d_until_at'] = $('#d_until_at_r').val();
            datos['h_until_at'] = $('#h_until_at_r').val();
            datos['h_since_at'] = $('#h_since_at_r').val();
            datos['type'] = $('#type_r').val();
            datos['description'] = $('#description_r').val();
            datos['ids'] = ids;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    // $('#createModal').modal('hide');
                    // updateCalendarData(response.calendardata);
                    // clean();
                    showMessageBox(response);
                    manageTableRegister.ajax.reload(null, false);
                },
                error: function(response) {
                    console.log(response);
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
    </script>
@endsection
