@extends('adminlte::page')

@section('title', 'Consultas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-inline text-bold">Lista de Empleados</h2>
                </div>
                <div class="card-body">
                    <form class="d-flex bd-highlight" action="{{ route('xlsx.getEmployeXlsx') }}" method="post">
                        {{ csrf_field() }}
                        <div class="p-2 bd-highlight" style="width: 200px">
                            <small>Gerencia</small>
                            <select name="gerencia" id="gerencia" class="form-control" onchange="loadSelect('areas',$('#gerencia').val())">
                                <option value="0">Todas</option>
                                @foreach (App\Models\Gerencia::all() as $item)
                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="p-2 bd-highlight" style="width: 200px">
                            <small>Areas</small>
                            <select name="areas" id="areas" class="form-control" onchange="loadSelect('funcion',$('#areas').val())">
                                <option value="0">Todas</option>
                            </select>
                        </div>
                        <div class="p-2 bd-highlight" style="width: 200px">
                            <small>Funcion</small>
                            <select name="funcion" id="funcion" class="form-control" >
                                <option value="0">Todas</option>
                            </select>
                        </div>
                        <div class="p-2 bd-highlight" style="width: 200px">
                            <small>Jornal/Destajo</small>
                            <select name="type" id="type" class="form-control" >
                                <option value="0">Todas</option>
                                @foreach (App\Models\Employes::select('type')->groupBy('type')->get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="p-2 bd-highlight" style="width: 200px">
                            <small>Tipo de empleado</small>
                            <select name="temploye" id="temploye" class="form-control" >
                                <option value="0">Todas</option>
                                @foreach (App\Models\EmployesType::all() as $item)
                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="p-2 bd-highlight">
                            <br>
                            <button type="submit" class="btn btn-success">Exportar en Excel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-inline text-bold">Proceso de Empleados</h2>
                </div>
                <div class="card-body">
                    {{-- <div > --}}
                        <form action="{{ route('xlsx.getEmployesProcessXlsx') }}" class="d-flex bd-highlight" method="post">
                            <div class="p-2 bd-highlight">
                                <small>Codigo de empleado</small>
                                <input id="codeOrdni" name="code" type="text" class="form-control" value="0" >
                            </div>
                            <div class="p-2 bd-highlight" style="width: 200px">
                                <small>Gerencia</small>
                                <select name="gerencia" id="gerencia2" class="form-control" onchange="loadSelect('areas',$('#gerencia2').val())">
                                    <option value="0">Todas</option>
                                    @foreach (App\Models\Gerencia::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="p-2 bd-highlight" style="width: 200px">
                                <small>Area</small>
                                <select name="areas" id="areas" class="form-control" >
                                    <option value="0">Todas</option>
                                </select>
                            </div>
                            <div class="p-2 bd-highlight">
                                <div>
                                    <small>Filtro por rango de dias</small>
                                </div>
                                <button type="button" class="btn btn-default" id="daterange-btn">
                                    <span>
                                        <i class="fa fa-calendar"></i> {{ date('Y-m-d'). " - " . date('Y-m-d') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                            <div class="p-2 bd-highlight">
                                <br>
                                    {{ csrf_field() }}
                                    <input type="hidden" id="start" name="start" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" id="end" name="end" value="{{ date('Y-m-d') }}">
                                    <button type="submit" class="btn btn-success">Exportar en Excel</button>
                            </div>
                        </form>
                    {{-- </div> --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    var url = '{{ url("/") }}';
    var start_date = "{{ date('Y-m-d') }}";
    var end_date = "{{ date('Y-m-d') }}";
    // var manageTable = null;
    $(document).ready(function(){

        $('#daterange-btn').daterangepicker(
        {
            ranges   : {
            'Hoy'       : [moment(), moment()],
            'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimos 7 Dias' : [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
            'Este Semana'  : [moment().startOf('week').day('1'), moment().endOf('week').day('7')],
            'Este Mes'  : [moment().startOf('month'), moment().endOf('month')],
            'Ultimo Mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
        },

        function (start, end) {
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            //loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD');
            $('#start').val(start_date);
            $('#end').val(end_date);
        }
    );

    });

    // function loadSelect(pfirst,psecond,from){
    //     console.log(from);
    //     if(from!=""){
    //         if($('#'+from).is('[disabled=disabled]')==false){
    //             psecond = $('#'+from).val();
    //         }else{
    //             return;
    //         }

    //     }

    //     if(pfirst!="codeOrdni"){
    //         $.ajax({
    //             url: url + "/"+pfirst+"/getData"+((psecond!="")?"/"+psecond:""),
    //             type: 'get',
    //             dataType: 'json',
    //             success: function(response){
    //                 $('#'+pfirst).append($('<option>').val(0).text("Todas"));
    //                 $.each(response , function (i,e){
    //                     // console.log(e.id);
    //                     $('#'+pfirst).append($('<option>').val(e.id).text(e.description));
    //                 });
    //             },
    //             error: function(response){
    //                 console.log(response);
    //             }
    //         })
    //     }
    // }

    function loadSelect(to,value){
        $.ajax({
                url: url + "/"+to+"/getData/"+value,
                type: 'get',
                dataType: 'json',
                success: function(response){
                    $('select[name='+to+']').children().remove();
                    $('select[name='+to+']').append($('<option>').val(0).text("Todas"));
                    $.each(response , function (i,e){
                        // console.log(e.id);
                        $('select[name='+to+']').append($('<option>').val(e.id).text(e.description));
                    });
                },
                error: function(response){
                    console.log(response);
                }
            })
    }




</script>
@endsection
