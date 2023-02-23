@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Feriados'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>Feriados</h2>
                        <button class="btn btn-primary m-1" onclick="create()">Agregar Feriados</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
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
                    <h4 class="modal-title">Nuevo Feriado</h4>
                </div>
                <form id="formcreate" action="{{ route('offday.create') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <small>Año <strong class="text-info">Solo en ese año se realizara el
                                        feriado.</strong></small>
                                <select name="year" id="year" class="form-control">
                                    <option selected value="">Todos los años</option>
                                    @for ($i = 2020; $i <= Carbon\Carbon::now()->format('Y'); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Mes</small>
                                <select name="month" id="month" class="form-control" required
                                    onchange="loadDays($(this).val())">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">
                                            {{ Carbon\Carbon::createFromFormat('m', $i)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Dia</small>
                                <select name="day" id="day" class="form-control" required>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Editar Feriado</h4>
                </div>
                <form id="formupdate" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <small>Año <strong class="text-info">Solo en ese año se realizara el
                                        feriado.</strong></small>
                                <select name="year" id="edit-year" class="form-control">
                                    <option selected value="NULL">Todos los años</option>
                                    @for ($i = 2020; $i <= Carbon\Carbon::now()->format('Y'); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Mes</small>
                                <select name="month" id="edit-month" class="form-control" required
                                    onchange="loadDays($(this).val())">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">
                                            {{ Carbon\Carbon::createFromFormat('m', $i)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <small>Dia</small>
                                <select name="day" id="edit-day" class="form-control" required>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
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
                    url: '{{ route('offday.getTable') }}',
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
                    url: url + '/offday/delete/' + id,
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
                url: url + '/auxiliar/offday/update/' + id,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#formupdate').attr('action', url + '/auxiliar/offday/update/' + id);
                    // $('#code-edit').val(response.data.code);
                    $('#edit-year').val((response.data.year) ? response.data.year : "NULL");
                    $('#edit-month').val(response.data.month);
                    $('#edit-day').val(response.data.day);
                    $('#editModal').modal('show');

                },
                error: function(ex) {
                    console.log(ex);
                }
            });

        }
    </script>
@endsection
