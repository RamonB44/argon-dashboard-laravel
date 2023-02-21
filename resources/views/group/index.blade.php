@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Editar grupos de trabajo'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start">
                        <h2>Grupos</h2>
                        <a class="btn btn-primary m-1" href="{{ route('group.create') }}">Agregar Grupo</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="manageTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Grupo</th>
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
@endsection

@section('css')
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" />
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
                    url: '{{ route('group.getTable') }}',
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
                    url: url + '/groups/delete/' + id,
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
    </script>
@endsection
