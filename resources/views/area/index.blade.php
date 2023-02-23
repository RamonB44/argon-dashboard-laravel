@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Areas'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md d-flex justify-content-center">
                                <h2>Areas</h2>
                            </div>
                            <div class="col-md">
                                <a class="btn btn-success btn-sm btn-block w-100" href="{{ route('areas.gestion') }}">Gestionar</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <small>Procesos</small>
                                <select name="cultivo" id="cultivo" class="form-control">
                                    @foreach (\App\Models\Procesos::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @php
                                $config = Auth::user()->getConfig();
                            @endphp
                            @if (count($config['sedes']) > 0)
                                <div class="col-md">
                                    <small>Sedes</small>
                                    <select name="sedes" id="sedes" class="form-control">
                                        {{-- <option value="0" selected>Todas las sedes</option> --}}

                                        @foreach (App\Models\Sedes::all() as $item)
                                            @if (in_array($item->id, $config['sedes']))
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md">
                                <button class="btn btn-primary btn-sm btn-block w-100 h-100" onclick="create()">Agregar Areas</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gerencia</th>
                                        <th>Area</th>
                                        <th>Color</th>
                                        <th>Tipo de CCosto</th>
                                        <th>C.Costo [ Agrupados ]</th>
                                        <th>Proceso</th>
                                        <th>Acciones</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formcreate" action="{{ route('area.create') }}" method="post">
                    <div class="modal-header">
                        <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true"></span></a>
                        <h4 class="modal-title">Nuevo Area</h4>
                    </div>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="offset-md-2 col-md-4">
                                <small>Gerencia</small>
                                <select name="gerencia" id="gerencia" class="form-control">
                                    @foreach ($gerencia as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Area</small>
                                <select name="name" id="name" class="form-control">
                                    @foreach (\App\Models\Area::all() as $item)
                                        <option value="{{ $item->area }}">{{ $item->area }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="name" id="name" class="form-control"> --}}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="offset-md-2 col-md-6">
                                <small>Centro de Costo</small>
                                <input type="text" id="ccosto" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <br>
                                <a href="javascript:addToArray($('#ccosto').val(),'tcosto')"
                                    class="btn btn-primary btn-block btn-lg">Agregar</a>
                            </div>
                        </div>
                        <div class="row justify-content-md-center">
                            <div class="table table-responsive col-md">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>C.Costo</th>
                                        <th>Accion</th>
                                    </thead>
                                    <tbody id="tcosto">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</a>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formupdate" method="post">
                    <div class="modal-header">
                        <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true"></span></a>
                        <h4 class="modal-title">Editar Area</h4>
                    </div>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="offset-md-2 col-md-4">
                                <small>Gerencia</small>
                                <select name="gerencia" id="gerencia-edit" class="form-control">
                                    @foreach ($gerencia as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Area</small>
                                <select name="name" id="name-edit" class="form-control">
                                    @foreach (\App\Models\Area::all() as $item)
                                        <option value="{{ $item->area }}">{{ $item->area }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="offset-md-2 col-md-6">
                                <small>Centro de Costo</small>
                                <input type="text" id="ccosto-edit" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <br>
                                <a href="javascript:addToArray($('#ccosto-edit').val(),'tcosto-edit')"
                                    class="btn btn-primary btn-block btn-lg">Agregar</a>
                            </div>
                        </div>
                        <div class="row justify-content-md-center">
                            <div class="table table-responsive col-md">
                                <!--<small>Agregar [ + ]</a></small>-->
                                <table class="table table-bordered">
                                    <thead>
                                        <th>#</th>
                                        <th>C.Costo</th>
                                        <th>Accion</th>
                                    </thead>
                                    <tbody id="tcosto-edit">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSend">Guardar</button>
                        <a type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</a>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@section('css')
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet" />
@endsection

@section('js')
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        var manageTable = null;
        var url = "{{ url('/') }}";
        var arrayCosto = Array();
        var message = {
            title: "Error",
            message: "El campo no puede estar vacio",
            icon: "error"
        }

        $(document).ready(function() {

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
                orderCellsTop: true,
                fixedHeader: true,
                bLengthChange: false,
                bVisible: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    type: 'get',
                    url: '{{ route('area.getTable') }}',
                    beforeSend: function() {
                        $('.progress').show();
                    },
                    complete: function() {
                        // $('.progress').hide();
                    },
                },
            });
        });

        function eliminar(id) {
            if (confirm('Estas seguro de eliminar este registro?')) {
                // Deleted it!
                var id_sede = $('#sedes').val();
                $.ajax({
                    url: url + '/areas/delete/' + id,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            // alert('eliminado correctamente :)');
                            showMessageBox(response);
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

        function create() {
            //set inputs in void
            $('#createModal').modal('show');
            // clean array
            arrayCosto = [];
            // load table from array
            loadArrayList("tcosto");
        }

        function edit(id) {
            var id_sede = $('#sedes').val();
            var id_proceso = $('#cultivo').val();
            $.ajax({
                url: url + '/areas/update/' + id + '/' + id_sede + '/' + id_proceso,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#formupdate').attr('action', url + '/areas/update/' + id + '/' + id_sede + '/' +
                        id_proceso);
                    // $('#code-edit').val(response.data.code);
                    $('#name-edit').val(response.data.area);
                    $('#gerencia-edit').val(response.data.id_gerencia);
                    $('#proceso-edit').val(response.data.id_proceso);
                    $('#hora_ingreso-edit').val(response.data.hora_ingreso);
                    $('#hora_salida-edit').val(response.data.hora_salida);
                    arrayCosto = [];
                    arrayCosto = JSON.parse(response.data.ccosto);
                    loadArrayList('tcosto-edit');
                    $('#editModal').modal('show');

                },
                error: function(ex) {
                    console.log(ex);
                }
            });

        }

        $('#sedes').on('change', function(e) {
            reloadTable();
        });

        $('#cultivo').on('change', function(e) {
            reloadTable();
        });

        function reloadTable() {
            // get value from sedes and proceso
            var id_sede = $('#sedes').val();
            var id_proceso = $('#cultivo').val();

            if (manageTable) {
                manageTable.destroy();
                manageTable = $('#manageTable').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    bLengthChange: false,
                    bVisible: false,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    ajax: {
                        type: 'get',
                        url: url + '/areas/getTable/' + id_sede + '/' + id_proceso,
                        beforeSend: function() {
                            $('.progress').show();
                        },
                        complete: function() {
                            // $('.progress').hide();
                        },
                    },
                });
            }
        }

        $('#formcreate').on('submit', function(e) {
            e.preventDefault();
            var data = {};
            $("form#formcreate :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                console.log($(e).attr('name'));
                data[$(e).attr('name')] = $(e).val();
            });
            var costo = $('form#formcreate :input[name="c_costo[]"]').serializeArray();
            // console.log(costo);
            var c_costo = Array();
            costo.forEach((e, i) => {
                c_costo[i] = e.value;
            });
            // console.log(c_costo);
            data["c_costo_c"] = c_costo;
            data["id_sede"] = $('#sedes').val();
            data["proceso"] = $('#cultivo').val();
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
                    $('#tcosto').children().remove();
                    arrayCosto = [];
                },
                error: function(response) {
                    alert(response);
                }
            });
        });

        $('#formupdate').on('submit', function(e) {
            e.preventDefault();
            var data = {};
            $("form#formupdate :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                console.log($(e).attr('name'));
                data[$(e).attr('name')] = $(e).val();
                // console.log(input);
            });

            var costo = $('form#formupdate :input[name="c_costo[]"]').serializeArray();
            var c_costo = Array();
            costo.forEach((e, i) => {
                c_costo[i] = e.value;
            });
            data["c_costo_c"] = c_costo;
            data["id_sede"] = $('#sedes').val();
            data["proceso"] = $('#cultivo').val();
            // daga["proceso"] = $('#proceso-edit').val();

            console.log(data);
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                data: data,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    $('#editModal').modal('hide');
                    showMessageBox(response);
                    manageTable.ajax.reload(null, false);
                    $('#tcosto-edit').children().remove();
                    arrayCosto = [];
                },
                error: function(response) {
                    alert(response);
                },
                // complete: function(){
                //     // $('#editModal').modal('toggle');
                // }
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


        function addToArray(ccosto, table) {

            if (ccosto.length <= 0) {
                message.title = "Error";
                message.message = "El campo no puede estar vacio";
                message.icon = "error";
                showMessageBox(message);
            } else {
                //before to add , check if ccosto is empty
                arrayCosto.push(ccosto);
                console.log("Agregado existosamente al array: " + ccosto);
                loadArrayList(table);
                console.log("Cargado: " + ccosto + " a " + table + ": " + arrayCosto);
                message.title = "Correcto";
                message.message = "Agregaste Correctamente";
                message.icon = "success";
                showMessageBox(message);
            }
        }

        function removeFromArray(ccosto, table) {
            // find element by costo number or index
            // var valor = arrayCosto[ccosto];
            // console.log(valor);
            arrayCosto = arrayCosto.filter(function(item) {
                return item != ccosto
            })

            // console.log(arrayCosto);
            loadArrayList(table)
        }

        function loadArrayList(table_id) {
            //  list array in table
            var table = $('#' + table_id);
            // console.log(table);
            // console.log(table_id);
            // console.log(arrayCosto.length)
            if (arrayCosto.length > 0) {
                var item = "";
                arrayCosto.forEach((e, i) => {
                    console.log(e);
                    item += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td><input type="input" name="c_costo[]" value="' + e + '" class="form-control"></td>' +
                        '<td><a class="text-danger" href="javascript:removeFromArray(' + e + ',\'' + table_id +
                        '\')">Eliminar</a></td>' +
                        '</tr>';
                });
                table.html(item);
            } else {
                console.log("no existen items");
                // clear elements in table
                table.children().remove();
            }
        }
    </script>
@endsection
