@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Cultivo'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>Procesos</h2>
                        <button class="btn btn-primary m-1" onclick="create()">Agregar Procesos</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descripcion</th>
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
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Nuevo Proceso</h4>
                </div>
                <form id="formcreate" action="{{ route('procesos.create') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <small>Proceso</small>
                                <input type="text" name="name" id="name" class="form-control">
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
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Editar Proceso</h4>
                </div>
                <form id="formupdate" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <small>Proceso</small>
                                <input type="text" name="name" id="name-edit" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSend">Guardar</button>
                </form>
                <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
            </div>
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
                columnDefs: [{
                    visible: false,
                    targets: null,
                    defaultContent: '',
                    orderable: false,
                    className: 'select-checkbox'
                }],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                ajax: {
                    type: 'get',
                    url: '{{ route('procesos.getTable') }}',
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
                $.ajax({
                    url: url + '/procesos/delete/' + id,
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
        }

        function edit(id) {
            $.ajax({
                url: url + '/procesos/update/' + id,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#formupdate').attr('action', url + '/procesos/update/' + id);
                    // $('#code-edit').val(response.data.code);
                    // $('#name-edit').val(response.data.fullname);
                    // $('#docnum-edit').val(response.data.doc_num);
                    $('#name-edit').val(response.data.name);
                    $('#editModal').modal('show');

                },
                error: function(ex) {
                    console.log(ex);
                }
            });

        }

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
                    $('#editModal').modal('hide');
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
