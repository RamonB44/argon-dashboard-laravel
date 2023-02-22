@extends('adminlte::page')

@section('title', 'Consultas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md">
                            <h2 class="text-center text-bold">Reportes</h2>
                        </div>
                        <div class="bd-highlight col-md" data-step="3" data-intro="el filtro por rango de fechas.">
                            <small>Filtro por fechas</small>
                            <button type="button" class="btn btn-default m-1 btn-sm btn-block" id="daterange-btn">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ date('Y-m-d') . " - " . date('Y-m-d') }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>

                    </div>
                    <div class="row">
                        <div hidden class="bd-highlight col-md-2" data-step="2" data-intro="este es el filtro por empleado, activa el switch y escribe el codigo.">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                <label class="custom-control-label" for="customSwitch1">Empleado</label>
                              </div>
                            <input id="codeOrdni" name="code" type="text" class="form-control" value="0" readonly>
                        </div>
                        @php
                        $config = Auth::user()->getConfig();
                        @endphp
                        @if(count($config['sedes']) > 0)
                        <div class="bd-highlight col-md-2" >
                            <small>Sedes</small>
                            <select name="sede" id="sede" class="form-control">
                            <option value="0" selected>Todas mis sedes</option>

                                @foreach (App\Sedes::all() as $item)
                                    @if(in_array($item->id,$config['sedes']))
                                        <option value="{{ $item->id }}" >{{ $item->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="bd-highlight col-md-2">
                            <small>Mis Procesos</small>
                            <select class="form-control" name="cultivo" id="cultivo">
                                <option value="ALL">Todos mis procesos</option>
                                @foreach ( \App\Procesos::whereIn('id', \DB::table('areas_sedes')->whereIn('id_sede',$config["sedes"])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray() )->get() as $item)
                                        <option value="{{ $item->id }}" >{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bd-highlight col-md">
                            <small>Areas</small>
                            <select name="area" id="area" class="form-control">
                                <option value="0" selected>Todas las areas</option>
                                @foreach (App\Area::all() as $item)
                                    @if (in_array($item->id,$config["areas"]))
                                        <option value="{{ $item->id }}">{{ $item->area }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="bd-highlight col-md">
                            <small>Turno</small>
                              <select class="form-control" name="turno" id="turno">
                                  <option value="0">Todos</option>
                                  <option>DIA</option>
                                  <option>NOCHE</option>
                                  <option>S/T</option>
                              </select>
                        </div>
                        <div class="bd-highlight col-md-1 ">
                            <br>
                            <div class="custom-control custom-switch mt-2 d-flex justify-content-center">
                                <input type="checkbox" class="custom-control-input" id="switch_check">
                                <label class="custom-control-label" for="switch_check">S/V</label>
                            </div>
                        </div>
                        <div class="bd-highlight col-md-1 ">
                            <br>
                            <div class="custom-control custom-switch mt-2 d-flex justify-content-center">
                                <input type="checkbox" class="custom-control-input" id="switch_f">
                                <label class="custom-control-label" for="switch_f">FALTAS</label>
                            </div>
                        </div>
                        <div class="bd-highlight col-md" data-step="4" data-intro="una vez elegido los filtros , hacer click aqui para mostrar los resultados">
                            <!--<br>-->
                            <a href="javascript:search(null)" class="btn btn-primary m-1 btn-lg btn-block">Buscar</a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="bd-highlight col-md" data-step="5" data-intro="y con este boton generaras un archivo excel con los resultados mas detallados y mejorado.">
                            <!--<br>-->
                            <form action="{{ route('xlsx.getAssisXlsx') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" id="start" name="start" value="{{ date('Y-m-d') }}">
                                <input type="hidden" id="end" name="end" value="{{ date('Y-m-d') }}">
                                <input type="hidden" id="sedes" name="sedes" value="0">
                                <input type="hidden" id="areas" name="areas" value="0">
                                <input type="hidden" id="turnos" name="turnos" value="0">
                                <button type="submit" class="btn btn-success  m-1 btn-sm btn-block">Exportar [Resumen]</button>
                            </form>
                        </div>
                        <div class="bd-highlight col-md">
                            <!--<br>-->
                            <form action="{{ route('xlsx.getAssisXlsxRRHH') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" id="start" name="start" value="{{ date('Y-m-d') }}">
                                <input type="hidden" id="end" name="end" value="{{ date('Y-m-d') }}">
                                <input type="hidden" id="sedes" name="sedes" value="0">
                                <input type="hidden" id="areas" name="areas" value="0">
                                <input type="hidden" id="turnos" name="turnos" value="0">
                                <button type="submit" class="btn btn-success m-1 btn-sm btn-block">Exportar [RR.HH]</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="overflow-x: auto;">
                    <div class="table">
                        <table class="table table-bordered" id="result">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
          <h4 class="modal-title">Editar Registro</h4>
        </div>
        <form id="formupdateRegister" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <small>Descripcion</small>
                        <select name="description" id="description_edit" class="form-control">
                            @foreach (App\Model\Auxiliar\TypeReg::all() as $item)
                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <small>Desde la fecha</small>
                       <input type="date" name="d_since_at" id="d_since_at_edit" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <small>Hasta la fecha</small>
                        <input type="date" name="d_until_at" id="d_until_at_edit"  value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <small>Desde la hora [24 hrs]</small>
                       <input type="time" name="h_since_at" id="h_since_at_edit" value="08:00" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <small>Hasta la hora [24 hrs]</small>
                        <input type="time" name="h_until_at" id="h_until_at_edit" value="17:00" class="form-control">
                    </div>
                </div>
            </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" >Guardar</button>
        </form>
        <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('plugins.Datatables', true)
@section('plugins.DataRangePickerJs', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    var url = '{{ url("/") }}';
    var start_date = "{{ date('Y-m-d') }}";
    var end_date = "{{ date('Y-m-d') }}";
    var dataTable = null;
    // var manageTable = null;
    $(document).ready(function(){

        if (RegExp('multipage', 'gi').test(window.location.search)) {

            introApp = introJs().setOption('doneLabel', 'Siguiente Pagina').start().oncomplete(function() {
                // window.location.href = url + '/users/config?multipage=true';
            });
        }

        $('#customSwitch1' ).on( "click", function(){
            if($('#customSwitch1').is(':checked')){
                $('#codeOrdni').attr('readonly',false);

            }else{
                // $('#sku').val("");
                $('#codeOrdni').val("0");
                $('#codeOrdni').attr('readonly',true);

            }
        });

        $('#daterange-btn').daterangepicker(
        {
            "showWeekNumbers": true,
            "locale": {
                "format": "MM/DD/YYYY",
                "separator": " - ",
                "applyLabel": "Aceptar",
                "cancelLabel": "Cancel",
                "fromLabel": "Desde",
                "toLabel": "A",
                "customRangeLabel": "Personalizada",
                "daysOfWeek": [
                    "Do",
                    "Lu",
                    "Ma",
                    "Mi",
                    "Ju",
                    "Vi",
                    "Sa"
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Setiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
                "firstDay": 1
            },
            ranges   : {
            'Hoy'       : [moment(), moment()],
            'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimos 7 Dias' : [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
            'Este Semana'  : [moment().startOf('week').day('1'), moment().endOf('week').day('7')],
            'Este Mes'  : [moment().startOf('month'), moment().endOf('month')],
            'Ultimo Mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment(),
            endDate  : moment(),
        },
        function (start, end) {
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            //loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD');
            $('input[name="start"]').val(start_date);
            $('input[name="end"]').val(end_date);
            }
        );

        search();

    });

    // setInterval(function(){
    //     search();
    // },180000);


    $('#sede').on('change',function(){
        console.log($(this).val());
        $('input[name="sedes"]').val($(this).val());
    });

    $('#area').on('change',function(){
        console.log($(this).val());
        $('input[name="areas"]').val($(this).val());
    });
    //we need to change this function
    function search(){
        //range max for query 7 days
        var codigo = $('#codeOrdni').val();
        var sede = $('#sede').val();
        var checked = $('#switch_check').is(':checked') ? 1 : 0;
        var area = $('#area').val();
        var turno = $('#turno').val();
        var salida = $('#switch_f').is(':checked') ? 1 : 0;
        var proceso = $('#cultivo').val();
        // console.log(checked);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: url+'/assistance/searchByEmploye',
            type: 'POST',
            data: {codigo:codigo,start_date:start_date,end_date:end_date,sede:sede,checked:checked,area:area,turno:turno,salida:salida,proceso:proceso},
            beforeSend:function(response){
                // $('#result').DataTable().destroy();
                if(dataTable){
                    $('#result').DataTable().destroy();
                    dataTable = null;
                }
                $('#result').children().remove( );
                $('#result').append("<h2>Cargando datos...</h2>");
            },
            error:function(response){
                console.log(response);

            },
            success:function(response){
                $('#result').children().remove( );
                $('#result').append(response);
                // console.log()

            },
            complete:function(response){
                console.log(response);
                if(!dataTable){
                    dataTable = $('#result').DataTable({
                        // responsive: true
                        orderCellsTop: true,
                        fixedHeader: true,
                        bLengthChange: true,
                        bVisible: true,
                        dom: 'Blfrtip',
                        pageLength : 50,
                        lengthMenu: [[50, 100, 150, 200], [50, 100, 200, 'Todos']],
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        fixedHeader: false
                    });
                    dataTable.fixedHeader.disable();
                    // new $.fn.dataTable.FixedHeader( dataTable );
                }

            }
        })

        // $('#response-table').DataTable();

    }
    
    function fixed(id){
        console.log("editando registreo :"+id);
        $.ajax({
            url: url+"/manageassistance/editRegister/"+id,
            method: 'get',
            success: function(response){
                console.log(response);
                if(response.success){
                    $('#description_edit').val(response.data.id_aux_treg);
                    $('#d_since_at_edit').val(response.data.d_since_at);
                    $('#d_until_at_edit').val(response.data.d_until_at);
                    $('#h_since_at_edit').val(response.data.h_since_at);
                    $('#h_until_at_edit').val(response.data.h_until_at);
                    $('#formupdateRegister').attr('action',url+"/manageassistance/editRegister/"+id);
                    $('#editModal').modal('show');
                }else{
    
                }
                // showMessageBox(response)
            },
            error: function(response){
                console.log(response);
            }
        })

    }
    
    $('#formupdateRegister').submit(function(e){
        e.preventDefault();
        var datos = {};
        datos['d_since_at'] = $('#d_since_at_edit').val();
        datos['d_until_at'] = $('#d_until_at_edit').val();
        datos['h_until_at'] = $('#h_until_at_edit').val();
        datos['h_since_at'] = $('#h_since_at_edit').val();
        datos['description'] = $('#description_edit').val();

        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: datos,
            success: function(response){
                console.log(response);

                // updateCalendarData(response.calendardata);
                // clean();
                showMessageBox(response);
                // $('#editModal').modal('hide');
            },
            error: function(response){
                console.log(response);
            }
        })

    });
    
    function showMessageBox(response){
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
