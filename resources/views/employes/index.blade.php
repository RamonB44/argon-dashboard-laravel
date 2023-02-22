@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Trabajadores'])

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" data-step="1"
                        data-intro="Modulo de empleados , aqui podremos gestionar los datos personales y de la empresa del personal.">
                        <div class="row">
                            <div class="bd-highlight col-md text-center">
                                <h2>Lista de Trabajadores</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" data-step="2"
                                data-intro="En esta parte llenamos la informacion del personal de manera individual para su registro">
                                <button class="btn btn-primary m-1 btn-sm btn-block h-100 w-100" onclick="create()">Nuevo
                                    Empleado</button>
                            </div>
                            {{-- <div class="bd-highlight col-md-2" data-step="3" data-intro="Previamente seleccionado elementos en la lista de personal/empleados y hacer click aqui , se imprimira los codigos de SAP en formato de codigo de Barra">
                            <button class="btn btn-secondary m-1 btn-sm btn-block" onclick="printBarcode()" >Imprimir Codigo</button>
                        </div> --}}
                            <div class="bd-highlight col-md-2" data-step="4"
                                data-intro="Previamente seleccionado elementos en la lista de personal/empleados y hacer click aqui , se procedera a cesar de manera indefinida al personal">
                                <button class="btn btn-danger m-1 btn-sm btn-block h-100 w-100" onclick="massiveDelete()">Cesado
                                    Unico/Masivo</button>
                            </div>
                            <div class="bd-highlight col-md-2" data-step="5"
                                data-intro="Previamente seleccionado elementos en la lista de personal/empleados y hacer click aqui , se procedera a reaundar al personal.">
                                <button class="btn btn-info m-1 btn-sm btn-block h-100 w-100" onclick="massiveRestore()">Reanudar
                                    Unico/Masivo</button>
                            </div>
                            <div class="bd-highlight col-md" data-step="6"
                                data-intro="Con esta herramienta podras importar datos del personal desde un archivo excel de tal manera evitando registrar de manera indivual.">
                                <!--<small>Importacion <strong>Empleados</strong></small>-->
                                <form class="mb-1" action="{{ route('employes.import') }}" method="post"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="file_xlsx" id="file_xlsx" class="custom-file-input"
                                                required>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-success h-100 w-100" id="">Subir</button>
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
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered" data-step="7" data-position="right"
                                data-intro="Lista de empleados, aqui podemos seleccionar los empleados que queramos imprimir codigos de SAP, Cesar y Reanudar.">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Codigo</th>
                                        {{-- <th>Codigo de validacion</th> --}}
                                        <th>Documento N°</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Area</th>
                                        <th>Funcion</th>
                                        <th>Proceso</th>
                                        <th>Sede</th>
                                        <th>CCosto</th>
                                        <th data-step="8" data-position="right"
                                            data-intro="Y tambien podemos generar un nuevo codigo de validacion, editar y eliminar el registro.">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="createModal">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Nuevo Trabajador</h4>
                </div>
                <form id="formcreate" action="{{ route('employes.create') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                <small>Codigo</small>
                                <input type="number" class="form-control" id="code" name="code"
                                    placeholder="Codigo" required>
                            </div>
                            <div class="col-md-2">
                                <small>Documento</small>
                                <input type="number" class="form-control" id="docnum" name="docnum"
                                    placeholder="Documento" required>
                            </div>
                            <div class="col-md-4">
                                <small>Nombres y Apellidos</small>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    placeholder="Nombres" required>
                            </div>
                            <div class="col-md-2">
                                <small>Telefono/Cel</small>
                                <input type="tel" class="form-control" id="telephone" name="telephone"
                                    placeholder="Telefono/Celular">
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                <small>Tipo de empleado</small>
                                <select name="t_emp" id="t_emp" class="form-control">
                                    <option value="1">OBRERO</option>
                                    <option value="2">EMPLEADO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Tipo</small>
                                <select name="type" id="type" class="form-control">
                                    <option value="JORNAL">JORNAL</option>
                                    <option value="DESTAJO">DESTAJO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Directo o Indirecto</small>
                                <!--<input class="form-control" id="remu" name="remu" type="number">-->
                                <select class="form-control" name="dir_ind" id="dir_ind">
                                    <option value="INDIRECTO">INDIRECTO</option>
                                    <option value="DIRECTO">DIRECTO</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <small>Remuneracion</small>
                                <input class="form-control" id="remu" name="remu" type="number"
                                    step=".01">
                            </div>
                            <div class="col-md-1">
                                <small>Tiene Hijos?</small>
                                <select class="form-control" name="hasChildren" id="hasChildren">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Turno</small>
                                <select name="turno" id="turno" class="form-control">
                                    <option value="DIA">DIA</option>
                                    <option value="NOCHE">NOCHE</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                @php
                                    $config = Auth::user()->getConfig();
                                @endphp
                                @if (count($config['sedes']) > 0)
                                    <small>Sedes</small>
                                    <select name="sede" id="sede" class="form-control" onchange="loadArea()">
                                        @foreach (App\Models\Sedes::all() as $item)
                                            @if (in_array($item->id, $config['sedes']))
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <small>Proceso</small>
                                <select name="proceso" id="proceso" class="form-control" onchange="loadArea()">
                                    @foreach ($proceso as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Area</small>
                                {{-- load area by proceso and sede --}}
                                <select name="area" id="area" class="form-control" onchange="loadFuncion()">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Funcion</small>
                                <select name="funcion" id="funcion" class="form-control">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>C.Costo</small>
                                <select name="c_costo" id="c_costo" class="form-control">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Editar Empleado</h4>
                </div>
                <form id="formupdate" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                <small>Codigo</small>
                                <input type="number" class="form-control" id="code-edit" name="code"
                                    placeholder="Codigo" required>
                            </div>
                            <div class="col-md-2">
                                <small>Documento</small>
                                <input type="number" class="form-control" id="docnum-edit" name="docnum"
                                    placeholder="Documento" required>
                            </div>
                            <div class="col-md-4">
                                <small>Nombres y Apellidos</small>
                                <input type="text" class="form-control" id="fullname-edit" name="fullname"
                                    placeholder="Nombres" required>
                            </div>
                            <div class="col-md-2">
                                <small>Telefono/Cel</small>
                                <input type="tel" class="form-control" id="telephone-edit" name="telephone"
                                    placeholder="Telefono/Celular">
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                <small>Tipo de empleado</small>
                                <select name="t_emp" id="t_emp-edit" class="form-control">
                                    <option value="1">OBRERO</option>
                                    <option value="2">EMPLEADO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Tipo</small>
                                <select name="type" id="type-edit" class="form-control">
                                    <option value="JORNAL">JORNAL</option>
                                    <option value="DESTAJO">DESTAJO</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Directo o Indirecto</small>
                                <!--<input class="form-control" id="remu" name="remu" type="number">-->
                                <select class="form-control" name="dir_ind" id="dir_ind-edit">
                                    <option value="INDIRECTO">INDIRECTO</option>
                                    <option value="DIRECTO">DIRECTO</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <small>Remuneracion</small>
                                <input class="form-control" id="remu-edit" name="remu" type="number"
                                    step=".01">
                            </div>
                            <div class="col-md-1">
                                <small>Tiene Hijos?</small>
                                <select class="form-control" name="hasChildren" id="hasChildren-edit">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Turno</small>
                                <select name="turno" id="turno-edit" class="form-control">
                                    <option value="DIA">DIA</option>
                                    <option value="NOCHE">NOCHE</option>
                                </select>
                            </div>

                        </div>
                        <div class="row">
                            <div class="offset-md-1 col-md-2">
                                @if (count($config['sedes']) > 0)
                                    <small>Sedes</small>
                                    <select name="sede" id="sede-edit" class="form-control"
                                        onchange="loadAreaEdit()">
                                        @foreach (App\Models\Sedes::all() as $item)
                                            @if (in_array($item->id, $config['sedes']))
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <small>Proceso</small>
                                <select name="proceso" id="proceso-edit" class="form-control" onchange="loadAreaEdit()">
                                    @foreach ($proceso as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Area</small>
                                <select name="area" id="area-edit" class="form-control" onchange="loadFuncionEdit()">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>Funcion</small>
                                <select name="funcion" id="funcion-edit" class="form-control">

                                </select>
                            </div>
                            <div class="col-md-2">
                                <small>C.Costo</small>
                                <select name="c_costo" id="c_costo-edit" class="form-control">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSend">Guardar</button>
                </form>
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
                    window.location.href = 'employes/manageprocess?multipage=true';
                });
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
                fixedHeader: false,
                bLengthChange: false,
                bVisible: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print',
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    }
                ],
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
                    url: '{{ route('employes.getTable') }}',
                    beforeSend: function() {
                        $('.progress').show();
                    },
                    complete: function() {
                        // $('.progress').hide();
                    },
                },
                "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData[10] == true) {
                        $('td', nRow).addClass('border border-danger');
                    } else {
                        $('td', nRow).addClass('border border-success');
                    }

                    return nRow;
                }
            });
        });

        function eliminar(id) {
            Swal.fire({
                title: 'Esta seguro?',
                text: "No sera posible revertir esta accion!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si , Eliminalo!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url + '/employes/delete/' + id,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);
                            showMessageBox(response);
                            manageTable.ajax.reload(null, false);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            })
        }

        function create() {
            //set inputs in void
            $('#createModal').modal('show');
            loadArea();
            loadFuncion();
        }

        function edit(id) {
            $.ajax({
                url: url + '/employes/update/' + id,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    showEdit(response, id);
                },
                error: function(ex) {
                    console.log(ex);
                }
            });
        }

        async function showEdit(response, id) {
            $('#formupdate').attr('action', url + '/employes/update/' + id);
            $('#code-edit').val(response.data.code);
            $('#fullname-edit').val(response.data.fullname);
            $('#docnum-edit').val(response.data.doc_num);
            $('#type-edit').val(response.data.type);
            $('#proceso-edit').val(response.data.id_proceso);
            $('#sede-edit').val(response.data.id_sede);
            $('#turno-edit').val(response.data.turno);
            $('#dir_ind-edit').val(response.data.dir_ind);
            $('#remu-edit').val(response.data.remuneracion);
            $('#t_emp-edit').val(response.data.id_employe_type);
            $('#hasChildren-edit').val(response.data.hasChildren);
            $('#telephone-edit').val(response.data.telephone_num);
            const loadA = await loadAreaEdit();
            //must be caougth before functionloadAreaEdit and loadFunctionEdit
            if (loadA === true) {
                // setTimeout((e)=>{
                $('#area-edit').val(response.data.funcion.areas.id);
                const loadF = await loadFuncionEdit();
                if (loadF === true) {
                    $('#funcion-edit').val(response.data.id_function);
                    $('#c_costo-edit').val(response.data.c_costo);
                }
                // },3000);
            }

            $('#editModal').modal('show');
        }

        // function printBarcode(){
        //     var ids = $.map(manageTable.rows('.selected').data(), function (item) {
        //         // return item[1]+item[2];
        //         return item[1];
        //     });

        //     console.log(ids)
        //     // alert(manageTable.rows('.selected').data().length + ' row(s) selected');
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     Swal.fire({
        //     title: 'Esta seguro?',
        //     // text: "You won't be able to revert this!",
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Si , Imprimelos!'
        //     }).then((result) => {
        //         if(result.value){
        //             $.ajax({
        //             url: '{{ ""/*route('employes.barcode')*/ }}',
        //             type: 'POST',
        //             data: {ids},
        //             dataType: 'json',
        //             success:function(response){
        //                 console.log(response);
        //                 showMessageBox(response);
        //             },
        //             error:function(response){
        //                 console.log(response);
        //             }
        //         });
        //         }
        //     })
        // }

        function massiveDelete() {
            var ids = $.map(manageTable.rows('.selected').data(), function(item) {
                // return item[1]+item[2];
                return item[1];
            });

            console.log(ids)
            // alert(manageTable.rows('.selected').data().length + ' row(s) selected');
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
                confirmButtonText: 'Si , Cesalos!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('employes.massiveDelete') }}',
                        type: 'POST',
                        data: {
                            ids
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            manageTable.ajax.reload(null, false);
                            showMessageBox(response);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            })

        }

        function generateNew(id) {
            if (confirm('Estas seguro de generar un nuevo codigo de validacion?')) {

                $.ajax({
                    url: url + '/employes/generateCode/' + id,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            alert('generado correctamente :)');
                            manageTable.ajax.reload(null, false);
                        }
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            } else {
                // Do nothing!
            }
        }

        function loadArea() {
            // this function load areas by process and sede
            // return new Promise( (resolve,reject) => {
            var id_sede = $('#sede').val();
            var id_proceso = $('#proceso').val();
            var elements = $('#area').children(); //get all childrens elements : option
            $.ajax({
                url: url + '/areas/loadAreas/' + id_sede + '/' + id_proceso,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#area').children().remove();
                    $.each(response, function(index, value) {
                        // alert( index + ": " + value );
                        $('#area').append($('<option>').val(value.id).text(value.area));
                    });
                    resolve(true);
                },
                error: function(response) {
                    console.log(response);
                    resolve(false);
                }

            });
            loadFuncion();
            // });
        }

        function loadFuncion() {
            setTimeout(function() {
                var id_area = $('#area').val();
                var id_proceso = $('#proceso').val();
                var id_sede = $('#sede').val();
                $.ajax({
                    url: url + '/funcion/loadByArea/' + id_area + '/' + id_sede + '/' + id_proceso,
                    method: 'get',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        $('#funcion').children().remove();
                        $.each(response.funcion, function(index, value) {
                            // alert( index + ": " + value );
                            $('#funcion').append($('<option>').val(value.id).text(value
                                .description));
                        });
                        $('#c_costo').children().remove();
                        // console.log(response.ccosto.ccosto);
                        $.each(JSON.parse(response.ccosto.ccosto), (i, v) => {
                            $('#c_costo').append($('<option>').val(v).text(v));
                        });

                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }, 1000);

        }

        function loadAreaEdit() {
            // debugger;
            return new Promise((resolve, reject) => {
                var id_sede = $('#sede-edit').val();
                var id_proceso = $('#proceso-edit').val();
                var elements = $('#area-edit').children(); //get all childrens elements : option
                $.ajax({
                    url: url + '/areas/loadAreas/' + id_sede + '/' + id_proceso,
                    method: 'get',
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        $('#area-edit').children().remove();
                        $.each(response, function(index, value) {
                            // alert( index + ": " + value );
                            $('#area-edit').append($('<option>').val(value.id).text(value
                            .area));
                        });
                        resolve(true);
                    },
                    error: function(response) {
                        console.log(response);
                        reject(response);
                    }
                });
            });
            // loadFuncionEdit();
        }

        function loadFuncionEdit() {
            return new Promise((resolve, reject) => {
                // setTimeout((e)=>{
                var id_area = $('#area-edit').val();
                var id_proceso = $('#proceso-edit').val();
                var id_sede = $('#sede-edit').val();
                $.ajax({
                    url: url + '/funcion/loadByArea/' + id_area + '/' + id_sede + '/' + id_proceso,
                    method: 'get',
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response);
                        $('#funcion-edit').children().remove();
                        $.each(response.funcion, function(index, value) {
                            // alert( index + ": " + value );
                            $('#funcion-edit').append($('<option>').val(value.id).text(value
                                .description));
                        });
                        $('#c_costo-edit').children().remove();
                        // console.log(response.ccosto.ccosto);
                        $.each(JSON.parse(response.ccosto.ccosto), (i, v) => {
                            $('#c_costo-edit').append($('<option>').val(v).text(v));
                        });
                        resolve(true);
                    },
                    error: function(response) {
                        console.log(response);
                        reject(response)
                    }
                });
                // else put resolve
                // },1000);
            });
        }

        function massiveRestore() {
            var ids = $.map(manageTable.rows('.selected').data(), function(item) {
                // return item[1]+item[2];
                return item[1];
            });

            // console.log(ids)

            // alert(manageTable.rows('.selected').data().length + ' row(s) selected');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // var result = showConfirmMessage();
            Swal.fire({
                title: 'Esta seguro?',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si , Restauralos!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('employes.massiveRestore') }}',
                        type: 'POST',
                        data: {
                            ids
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            showMessageBox(response);
                            manageTable.ajax.reload(null, false);
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            })
            // manageTable.ajax.reload( null, false );
        }

        $('#formupdate').on('submit', function(e) {
            e.preventDefault();
            var data = {};
            $("form#formupdate :input").each(function(e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                data[$(this).attr('name')] = $(this).val();
                // console.log(input);
            });

            console.log(data);
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                data: data,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    // $('#editModal').modal('hide');
                    showMessageBox(response);
                    manageTable.ajax.reload(null, false);
                },
                error: function(response) {
                    alert(response);
                },
                // complete: function(){
                //     // $('#editModal').modal('toggle');
                // }
            });
        });

        $('#formcreate').on('submit', function(e) {
            e.preventDefault();
            var data = {};
            $("form#formcreate :input").each(function(e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                data[$(this).attr('name')] = $(this).val();

            });

            console.log(data);
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                data: data,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    $('#createModal').modal('hide');
                    showMessageBox(response);
                    manageTable.ajax.reload(null, false);
                },
                error: function(response) {
                    alert(response);
                }
            });
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
