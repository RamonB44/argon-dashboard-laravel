@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Tipo de Registro'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>TipoRegistro</h2>
                        <button class="btn btn-primary m-1" onclick="create()">Agregar TipoRegistro</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Abreviatura</th>
                                        <th>Descripcion</th>
                                        <th>Color</th>
                                        <th>RegistroAdicional?</th>
                                        <th>Remunerado?</th>
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
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Nueva TipoRegistro</h4>
                </div>
                <form id="formcreate" action="{{ route('treg.create') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <small>Abreviatura</small>
                                <input type="text" name="abr" id="abr" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <small>Color</small>
                                <input type="color" name="color" id="color" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <small>Descripcion</small>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="aditionable" id="aditionable" type="checkbox" >
                                    <label class="form-check-label" for="aditionable">Registro Adicional?</label>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="is_paid" id="is_paid" type="checkbox" >
                                    <label class="form-check-label" for="is_paid">Remunerado?</label>
                                </div>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Editar TipoRegistro</h4>
                </div>
                <form id="formupdate" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <small>Abreviatura</small>
                                <input type="text" name="abr" id="edit-abr" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <small>Color</small>
                                <input type="color" name="color" id="edit-color" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <small>Descripcion</small>
                                <input type="text" name="name" id="edit-name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="aditionable" id="edit-aditionable" type="checkbox" >
                                    <label class="form-check-label" for="aditionable">Registro Adicional?</label>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="is_paid" id="edit-is_paid" type="checkbox">
                                    <label class="form-check-label" for="is_paid">Remunerado?</label>
                                </div>
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
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet" />
@endsection

@section('js')
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script>
        var manageTable = null;
        var url = "{{ url('/') }}";
        $(document).ready(function() {

            //Timepicker
            // $('#timepicker').datetimepicker({
            //   format: 'LT'
            // });

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
                    url: '{{ route('treg.getTable') }}',
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
                    url: url + '/auxiliar/treg/delete/' + id,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            alert('eliminado correctamente :)');
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
                url: url + '/auxiliar/treg/update/' + id,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#formupdate').attr('action', url + '/auxiliar/treg/update/' + id);
                    // $('#code-edit').val(response.data.code);
                    $('#edit-name').val(response.data.description);
                    $('#edit-abr').val(response.data.abr);
                    $('#edit-color').val(response.data.color);
                    $('#edit-is_paid').prop('checked', (response.data.is_paid == 1) ? true : false);
                    $('#edit-aditionable').prop('checked', (response.data.aditionable == 1) ? true : false);
                    $('#editModal').modal('show');

                },
                error: function(ex) {
                    console.log(ex);
                }
            });

        }
    </script>
@endsection
