@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Usuarios'])
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-start">
                    <div class="d-flex bd-highlight">
                        <div class="p-2 bd-highlight">
                            <h2>Usuarios</h2>
                        </div>
                        <div class="p-2 bd-highlight">
                            <a class="btn btn-primary m-1" href="{{ route('users.create_form') }}" >Agregar Usuarios</a>
                        </div>
                        <div class="p-2 bd-highlight">
                            <button disabled class="btn btn-danger m-1" onclick="massiveDelete()" >Cesado Unico/Masivo</button>
                        </div>
                        <div class="p-2 bd-highlight">
                            <button disabled class="btn btn-info m-1" onclick="massiveRestore()" >Restaurar Unico/Masivo</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manageTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Creado a</th>
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


@section('plugins.Datatables', true)
@section('js')
<script>
var manageTable = null;
var url = "{{ url('/') }}";
$(document).ready(function(){
    $('#manageTable thead tr').clone(true).appendTo( '#manageTable thead' );

    $('#manageTable thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Buscar '+title+'" />' );

        $( 'input', this ).on( 'keyup change', function () {
            if ( manageTable.column(i).search() !== this.value ) {
                manageTable
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );

    manageTable = $('#manageTable').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        bLengthChange: false,
        bVisible: false,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [ {
            visible: false,
            targets: null,
            defaultContent: '',
            orderable: false,
            className: 'select-checkbox'
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        ajax: {
            type : 'get',
            url: '{{ route("users.getTable") }}',
            beforeSend: function () {
                $('.progress').show();
            },
            complete: function () {
                // $('.progress').hide();
            },
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull)    {
            if ( aData[3] == "Inactivo" )
            {
                $('td', nRow).addClass('border border-danger');
            }
            else
            {
                $('td', nRow).addClass('border border-success');
            }

            return nRow;
        }
    });
});

function eliminar(id){
        if (confirm('Estas seguro de eliminar este registro?')) {
            // Deleted it!
            $.ajax({
                url: url+'/users/delete/'+id,
                type: 'GET',
                success:function(response){
                    console.log(response);
                    if(response){
                        alert('eliminado correctamente :)');
                        manageTable.ajax.reload( null, false );
                    }
                },
                error:function(response){
                    console.log(response);
                }
            });
        } else {
            // Do nothing!
        }

}

</script>
@endsection
