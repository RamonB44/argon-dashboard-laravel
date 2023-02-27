@extends('layouts.app-gerencia')

@section('title', 'Gerencia')

@section('content')
{{-- <div class="container"> --}}
        <div class="row justify-content-between mb-2">
            <div class="col-md-6 bd-highlight text-center" >
                <ul class="list-group list-group-horizontal-sm m-auto" style="border-color: rgb(93, 60, 129)">
                    <!--<li class="list-group-item" style="background-color: green">-->
                    <!--    <a href="javascript:reloadAll()" style="color: #ffffff;font-weight: bold">A.Total : <strong id="total-type">100</strong></a>-->
                    <!--</li>-->
                    <li class="list-group-item" style="background-color: green">
                        <a href="javascript:setTime('DIA')" style="color: #ffffff;font-weight: bold">Asistencia Dia : <strong id="total-dia">100</strong></a>
                    </li>
                    <li class="list-group-item" style="background-color: rgb(93, 60, 129)">
                        <a href="javascript:setTime('NOCHE')" style="color: #ffffff;font-weight: bold">Asistencia Noche : <strong id="total-noche">100</strong></a>
                    </li>
                    <li class="list-group-item text-center" style="background-color: lightskyblue;">
                        <a href="javascript:listWorkers(0,'V');"  style="color: #000000;font-weight:bold">
                            Verificado :
                            <strong id="total_v">100</strong>
                        </a>
                    </li>
                    <li class="list-group-item text-center" style="background-color: red;"><a href="javascript:listWorkers(0,'SV');" style="color: #000000;font-weight:bold">
                        Falta Verificar : <strong id="total_sv">100</strong></a></li>
                </ul>
            </div>
            <div class="col-md-2 bd-highlight text-center">
                <ul class="list-group list-group-horizontal-sm m-auto" style="border-color: rgb(93, 60, 129)">
                    <!--<li class="list-group-item list-group-item-danger" style="background-color: #f5c54c;color: #000000;font-weight:bold">TRU : <strong id="total_tru">100</strong></li>-->
                    <li class="list-group-item list-group-item-danger"style="background-color: #f5c54c;">
                        <a href="javascript:listWorkers(0,'SM')" style="color: #000000;font-weight:bold">
                            FA : <strong id="total_sm">0</strong></a>
                    </li>
                    <li class="list-group-item list-group-item-warning"style="background-color: #935f00">
                        <a href="javascript:listWorkers(0,'SR')" style="color: #000000;font-weight:bold">
                            FR : <strong id="total_sr">0</strong></a>
                    </li>
                </ul>
            </div>
            @php
                $config = Auth::user()->getConfig();
                $main_sede = $config["sedes"][0];
            @endphp
            @if(count($config['sedes']) > 0)
            <div hidden class="col-md bd-highlight mb-2">
                <small>Sedes</small>
                <select name="sedes" id="sedes" class="form-control">
                    @foreach (App\Models\Sedes::all() as $item)
                        @if(in_array($item->id,$config['sedes']))
                            <option value="{{ $item->id }}" >{{ $item->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md bd-highlight mb-2">
                <small>Mis Procesos</small>
                <select class="form-control" name="cultivo" id="cultivo">
                    <option value="ALL">Todos mis procesos</option>
                    @foreach ( App\Models\Procesos::whereIn('id', \DB::table('areas_sedes')->where('id_sede',$main_sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray() )->get() as $item)
                            <option value="{{ $item->id }}" >{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md bd-highlight m-auto" data-step="3" data-intro="Con este boton controlas el rango de fechas de todos los graficos.">
                <button type="button" class="btn btn-success btn-sm btn-block" id="daterange-btn">
                    <span>
                        <i class="fa fa-calendar"></i> {{ date('Y/m/d') }} - {{ date('Y/m/d') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>
        </div>
        <div id="response_jefes" class="row text-white text-center mb-3" style="border-bottom: 1px solid rgb(93, 60, 129);border-top: 1px solid rgb(93, 60, 129)">
            <div class="dropdown col-md py-2" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545; " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu Item 1
                </a>

                <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Sub Menu Item</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </div>

            <div class="dropdown col-md py-2" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu Item 1
                </a>

                <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Sub Menu Item</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </div>

            <div class="dropdown col-md py-2" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                <a href="#" class="disabled" id="dropdownMenuLink" style="color:#89b545;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu Item 1
                </a>

                <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Sub Menu Item</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </div>

            <div class="dropdown col-md py-2" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu Item 4
                </a>

                <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Sub Menu Item</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </div>

            <div class="dropdown col-md py-2" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                <a href="#" class="disabled" id="dropdownMenuLink" style="color:#89b545;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Menu Item 5
                </a>

                <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Sub Menu Item</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </div>
        </div>
        <div id="carouselExampleFade" class="carousel slide carousel-fade row" data-ride="carousel" >
            <div class="carousel-indicators">
                <li data-target="#carouselExampleFade" data-bs-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleFade" data-bs-slide-to="1"></li>
                <li data-target="#carouselExampleFade" data-bs-slide-to="2"></li>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" >
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header text-center text-white badge-info" style="background-color: rgb(93, 60, 129)">
                                    TIPO DE EMPLEADO
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <div class="chartjs-size-monitor">
                                            <div class="chartjs-size-monitor-expand">
                                                <div class="">
                                                </div>
                                            </div>
                                            <div class="chartjs-size-monitor-shrink">
                                                <div class="">
                                                </div>
                                            </div>
                                        </div>
                                        <canvas id="pieChart" style="height: 400px; min-height: 230px; display: block; width: 400px;" width="487" height="230" class="chartjs-render-monitor">
                                        </canvas>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header text-center text-white badge-info" style="background-color: rgb(93, 60, 129)">
                                    TIPO DE COSTO
                                </div>
                                <div class="card-body">
                                    <div class="chart">
                                        <div class="chartjs-size-monitor">
                                            <div class="chartjs-size-monitor-expand">
                                                <div class="">
                                                </div>
                                            </div>
                                            <div class="chartjs-size-monitor-shrink">
                                                <div class="">
                                                </div>
                                            </div>
                                        </div>
                                        <canvas id="pieChartDI" style="height: 400px; min-height: 230px; display: block; width: 400px;" width="487" height="230" class="chartjs-render-monitor">
                                        </canvas>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                    <div id="response-details" class="row" style="border-top: 0.5px solid rgb(93, 60, 129);overflow-x:auto"></div>
                </div>
                <div class="carousel-item" >
                    <div class="table row m-auto" style="overflow-x:auto">
                            <table id="trabajadores" class="table table-hover" style="border-top: 0.5px solid rgb(93, 60, 129)">
                                    <thead>
                                        <tr>
                                            <td class="">#</td>
                                            <td class="">Codigo</td>
                                            <td class="">Nombres</td>
                                            <td class="">Area</td>
                                            <td class="">Hora y Fecha</td>
                                        </tr>
                                    </thead>
                                    <tbody id="response-workers">

                                    </tbody>
                            </table>
                    </div>
                </div>
                <div class="carousel-item" >
                    <div class="row">
                        <div class="bd-highlight col-md-2" data-step="2" data-intro="este es el filtro por empleado, activa el switch y escribe el codigo.">
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
                            <div class="bd-highlight col-md-2" hidden>
                                <small>Sedes</small>
                                <select name="sede" id="sede" class="form-control">
                                <option value="0" selected>Todas las sedes</option>

                                    @foreach (App\Models\Sedes::all() as $item)
                                        @if(in_array($item->id,$config['sedes']))
                                            <option value="{{ $item->id }}" >{{ $item->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="bd-highlight col-md-2">
                                <small>Areas</small>
                                <select name="area" id="area" class="form-control">
                                    <option value="0" selected>Todas las areas</option>
                                    @foreach (App\Models\Area::all() as $item)
                                        @if (in_array($item->id,$config["areas"]))
                                            <option value="{{ $item->id }}">{{ $item->area }}</option>
                                        @endif
                                    @endforeach
                                </select>
                        </div>
                            <div class="bd-highlight col-md-2">
                                <small>Turno</small>
                                <select class="form-control" name="turno" id="turno">

                                    <option value="0">Todos</option>
                                    @php
                                            $time = Carbon\Carbon::now()->timezone('America/Lima');
                                            $morning = Carbon\Carbon::create($time->year, $time->month, $time->day, 4, 0, 0); //set time to 04:00 : 4AM
                                            $evening = Carbon\Carbon::create($time->year, $time->month, $time->day, 14, 0, 0); //set time to 14:00 : 2PM
                                    @endphp

                                    @if($time->between($morning, $evening, true))
                                    <option selected>DIA</option>
                                    <option>NOCHE</option>
                                    @else
                                    <option>DIA</option>
                                    <option selected>NOCHE</option>
                                    @endif

                                    <option>S/T</option>

                                </select>
                        </div>
                            <div class="bd-highlight col-md-1">
                                <br>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="switch_check">
                                        <label class="custom-control-label" for="switch_check">S/V</label>
                                </div>
                        </div>
                            <div class="bd-highlight col-md-1" data-step="4" data-intro="una vez elegido los filtros , hacer click aqui para mostrar los resultados">
                                <!--<br>-->
                                <a href="javascript:search(null)" class="btn btn-primary  m-1 btn-sm btn-block">Buscar</a>
                            </div>
                            <div class="bd-highlight col-md-2" data-step="5" data-intro="y con este boton generaras un archivo excel con los resultados mas detallados y mejorado.">
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
                            <div class="bd-highlight col-md-2">
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
                    <div class="row m-auto mb-1" style="overflow-x:auto">
                        <div class="table">
                            <table class="table table-bordered" id="result">
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
{{-- </div> --}}
@endsection

{{-- @section('plugins.Chartjs', true) --}}
{{-- add chart js plugin --}}
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/green/pace-theme-flash.min.css" />
<style>
    .dropdown{
        display: inline-block;
    }

    .dropdown:hover, .dropdown:focus {
            background-image:none !important;
    }
    .dropdown:hover, .dropdown:focus {
            background-color:rgb(93, 60, 129);
            /* color: #ffffff; */
            /* color: red; */
    }

    .dropdown:hover > a, .dropdown:focus > a {
            background-image:none !important;
            /* background: transparent !important; */
    }

    .dropdown:hover > a, .dropdown:focus > a {
            /* background-color: red; */
            color: #ffffff !important;
            /* color: red; */
    }



    .dropdown-menu > a:hover, .dropdown-menu > a:focus {
            background-image:none !important;
    }
    .dropdown-menu > a:hover, .dropdown-menu > a:focus {
            background-color:rgb(93, 60, 129);
            color: #ffffff;
            /* color: red; */
    }


    .dropdown:hover>.dropdown-menu {
        display: inline-block;
    }

    .dropdown>.dropdown-toggle:active {
        /*Without this, clicking will make it sticky*/
        pointer-events: none;
    }

</style>
@endsection
@section('js')
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
{{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script> --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script>
    var pieChartData;
    var pieChartData2;
    var pieChart;
    var pieChart3;
    var url = "{{ url('/') }}";
    var start_date = "{{ date('Y-m-d') }}";
    var end_date = "{{ date('Y-m-d') }}";
    var user = 0;
    var checked = 0;
    var switch_pulse = true;
    var dataTable = null;
    var time = "DIA";
    var dataTableL = null;

    var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

    var getRandomColor = function() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    $(document).ready(function(){
        //asistencias/inasistencia/permisos data;
        // $('.carousel').carousel('pause');
        $('.carousel').carousel({
            pause: true,
            interval: false,
        });
        pieChartData = {
            labels: ['DESTAJEROS','JORNALES'],
                datasets: [
                    {
                    data: [randomScalingFactor(),randomScalingFactor()],
                    backgroundColor : ["blue", "red"],
                    }
            ]
        }
        pieChartData2 = {
            labels: ['DIRECTOS','INDIRECTOS'],
                datasets: [
                    {
                    data: [randomScalingFactor(),randomScalingFactor()],
                    backgroundColor : ["rgb(93, 60, 129)", "green"],
                    }
            ]
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

        loadGraphics(start_date, end_date);
        search();
    });

    setInterval(function(){
        loadGraphics(start_date, end_date);
    },60000);

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
            $('#daterange-btn span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'))
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD');
            $('input[name="start"]').val(start_date);
            $('input[name="end"]').val(end_date);
            user = 0;
            loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            search();
        }
    );

    $('#sedes').change(function(e){
        loadGraphics(start_date,end_date);
    });

    $('#cultivo').change(function(e){
        loadGraphics(start_date,end_date);
    });

    $('#reporteCarousel').click(function(e){
        $('.carousel').carousel(2);
    });

    var contador = 0;
    var total = 0;

    function loadGraphics(start,end){
        var sedes = $('#sedes').val();
        var proceso = $('#cultivo').val();

        if(!user){

            $.ajax({
                url: url+'/home/loadAssistances/'+start+'/'+end+'/'+sedes+'/'+proceso,
                type: 'GET',
                success: function(response){
                    $('#total-dia').html(response.dia);
                    $('#total-noche').html(response.noche);
                },
                error: function(response){
                    console.log(response);
                }
            });

            $.ajax({
                    url: url+'/home/loadGraphicsbyType/'+start+'/'+end+'/'+sedes+'/'+checked+'/'+time+'/'+proceso,
                    type: 'GET',
                    success:function(response){
                        console.log(response);
                        if(response.success){
                            updatePieData2(response.data,response.labels);
                            $('#total-type').text(response.total);
                            total = response.total;
                        }else{
                            $('#total-type').text("0");
                            // $('#total-asistencia').text("0");
                            removePieData2();
                        }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });

            $.ajax({
                    url: url+'/home/loadGraphicsbydirind/'+start+'/'+end+'/'+sedes+'/'+checked+'/'+time+'/'+proceso,
                    type: 'GET',
                    success:function(response){
                        console.log(response);
                        if(response.success){
                            updatePieData3(response.data,response.labels);
                            // $('#total-type').text(response.total);

                        }else{
                            $('#total-type').text("0");
                            // $('#total-asistencia').text("0");
                            removePieData3();
                        }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });
            //get asistance from user jefe areas by sede gerencia
            $.ajax({
                url: url+'/home/loadAssistancebyUser/'+start+'/'+end+'/'+sedes+'/'+time+'/'+proceso,
                type: "get",
                dataType: "json",
                success: function(res){
                    // console.log(res);
                    $('#response_jefes').html(res.response);
                    $('#total_v').html(res.total_v);
                    $('#total_sm').html(res.total_sm);
                    $('#total_tru').html((res.total_v + res.total_sm));
                    $('#total_sv').html(res.total - res.total_v);
                    $('#total_sr').html(res.total_sr);
                },
                error: function(res){
                    console.log(res);
                }
            });

            $.ajax({
                    url: url+'/home/loadAssistancebyArea/'+start+'/'+end+'/'+sedes+'/0/'+checked+'/'+time+'/'+0+'/'+proceso,
                    type: 'GET',
                    success:function(response){
                        console.log(response);
                        $('#response-details').html(response);
                        // if(response.success){
                        //     updatePieData2(response.data,response.labels);
                        // }else{
                        //     $('#total-type').text("0");
                        //     // $('#total-asistencia').text("0");
                        //     removePieData2();
                        // }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });
        }else{
            $.ajax({
                    url: url+'/home/loadGraphicsbyTypeUser/'+start+'/'+end+'/'+user+'/'+time,
                    type: 'GET',
                    success:function(response){
                        console.log(response);

                        if(response.success){
                            updatePieData2(response.data,response.labels);
                            showMessageBox(response);
                            // $('#total-type').text(response.total);

                        }else{
                            // $('#total-type').text("0");
                            showMessageBox(response);
                            // $('#total-asistencia').text("0");
                            removePieData2();
                        }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });

            $.ajax({
                    url: url+'/home/loadGraphicsbydirindUser/'+start+'/'+end+'/'+user+'/'+time,
                    type: 'GET',
                    success:function(response){
                        console.log(response);
                        if(response.success){
                            updatePieData3(response.data,response.labels);
                            // $('#total-type').text(response.total);

                        }else{
                            // $('#total-type').text("0");
                            // $('#total-asistencia').text("0");
                            removePieData3();
                        }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });

            $.ajax({
                    url: url+'/home/loadAssistancebyArea/'+start+'/'+end+'/'+sedes+'/'+user+"/1/"+time,
                    type: 'GET',
                    success:function(response){
                        console.log(response);
                        $('#response-details').html(response);
                        // if(response.success){
                        //     updatePieData2(response.data,response.labels);
                        // }else{
                        //     $('#total-type').text("0");
                        //     // $('#total-asistencia').text("0");
                        //     removePieData2();
                        // }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });
        }

    }

    function updatePieData2(data,label) {
        // barChart.data.labels.push(label);

        pieChart2.data.labels = label;
        var color = [
            getRandomColor(),
            getRandomColor(),
        ]
        pieChart2.data.datasets.forEach((dataset) => {
            //we need potiyion data
            // console.log(data[contador]);
            dataset.data = data;
            // dataset.backgroundColor = color;
            // contador++;
        });
        pieChart2.update();
        contador = 0;

    }

    function removePieData2() {

        pieChart2.data.labels = [];
        pieChart2.data.datasets.forEach((dataset) => {
        //
            dataset.data = [];
            // dataset.backgroundColor = [];
        });
        pieChart2.update();

    }

    function updatePieData3(data,label) {
        // barChart.data.labels.push(label);

        pieChart3.data.labels = label;
        var color = [
            getRandomColor(),
            getRandomColor(),
        ]
        pieChart3.data.datasets.forEach((dataset) => {
            //we need potiyion data
            // console.log(data[contador]);
            dataset.data = data;
            // dataset.backgroundColor = color;
            // contador++;
        });
        pieChart3.update();
        contador = 0;

    }

    function removePieData3() {

        pieChart3.data.labels = [];
        pieChart3.data.datasets.forEach((dataset) => {
        //
            dataset.data = [];
            // dataset.backgroundColor = [];
        });
        pieChart3.update();

    }

    $(function () {
        Chart.pluginService.register({
            beforeRender: function(chart) {
                if (chart.config.options.showAllTooltips) {
                // create an array of tooltips
                // we can't use the chart tooltip because there is only one tooltip per chart
                chart.pluginTooltips = [];
                chart.config.data.datasets.forEach(function(dataset, i) {
                    chart.getDatasetMeta(i).data.forEach(function(sector, j) {
                    chart.pluginTooltips.push(new Chart.Tooltip({
                        _chart: chart.chart,
                        _chartInstance: chart,
                        _data: chart.data,
                        _options: chart.options.tooltips,
                        _active: [sector]
                    }, chart));
                    });
                });

                // turn off normal tooltips
                chart.options.tooltips.enabled = false;
                }
            },
            afterDraw: function(chart, easing) {
                if (chart.config.options.showAllTooltips) {
                // we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
                if (!chart.allTooltipsOnce) {
                    if (easing !== 1)
                    return;
                    chart.allTooltipsOnce = true;
                }

                // turn on tooltips
                chart.options.tooltips.enabled = true;
                Chart.helpers.each(chart.pluginTooltips, function(tooltip) {
                    tooltip.initialize();
                    tooltip.update();
                    // we don't actually need this since we are not animating tooltips
                    tooltip.pivot();
                    tooltip.transition(easing).draw();
                });
                chart.options.tooltips.enabled = false;
                }
            }
        });

        var pieChartCanvas3 = $('#pieChart').get(0).getContext('2d')
        var pieChartCanvas4 = $('#pieChartDI').get(0).getContext('2d')

        var pieChartOptions = {
            legend: {
                        onClick: (e) => e.stopPropagation(),
                        "labels": {
                            "fontSize": 15,
                        },
                        position: "top",
                        align: "start"
                    },
            maintainAspectRatio : false,
            responsive: true,
            cutoutPercentage: 0,
            tooltips: {
                enabled: false,
                caretPadding: 20,
                xAlign: 'left'
            },
            plugins: {
                datalabels: {
                    // formatter: (value, ctx) => {
                    //     let sum = ctx.dataset._meta[0].total;
                    //     let percentage = value + " ( " + (value * 100 / sum).toFixed(2) + "% )";
                    //     return percentage;
                    // },
                    color: '#fff',
                    font: {
                      weight: 'bold',
                      size: 16,
                    }
                }
            },
            // showAllTooltips: true
        }

        var pieChartOptions2 = {
            legend: {
                        onClick: (e) => e.stopPropagation(),
                        "labels": {
                            "fontSize": 15,
                        },
                        position: "top",
                        align: "start"
                    },
            maintainAspectRatio : false,
            responsive: true,
            cutoutPercentage: 0,
            tooltips: {
                enabled: false,
                caretPadding: 20,
            },
            plugins: {
                datalabels: {
                    // formatter: (value, ctx) => {
                    //     let sum = ctx.dataset._meta[1].total;
                    //     let percentage = value + " ( " + (value * 100 / sum).toFixed(2) + "% )";

                    //     return percentage;
                    // },
                    font: {
                      weight: 'bold',
                      size: 16,
                    },
                    color: '#fff',
                }
            },
            // showAllTooltips: true
        }


        pieChart2 = new Chart(pieChartCanvas3, {
            type: 'pie',
            data: pieChartData,
            options: pieChartOptions
        })

        pieChart3 = new Chart(pieChartCanvas4, {
            type: 'pie',
            data: pieChartData2,
            options: pieChartOptions2
        })
    })

    function showMessageBox(response){
        // Swal.fire({
        //     // position: 'top-end',
        //     icon: response.icon,
        //     title: response.title,
        //     text: response.message,
        //     showConfirmButton: false,
        //     timer: 1500
        // })
    }

    function reloadChart(id_user){
        user = id_user;
        loadGraphics(start_date, end_date);
        $('.nav-item').removeClass('active');
        $('#n-1').addClass('active');
        $('#carouselExampleFade').carousel(0);
        // test();
    }

    function reloadAll(){
        user = 0;
        checked = 0;
        loadGraphics(start_date,end_date);
        $('.nav-item').removeClass('active');
        $('#n-1').addClass('active');
        $('#carouselExampleFade').carousel(0);
        // test();
    }

    function setChecked(){
        checked = 1;
        user = 0;
        loadGraphics(start_date,end_date);
    }

    function listWorkers(id_user,type){
        var id_sede = $('#sedes').val();
        var proceso = $('#cultivo').val();
            $.ajax({
                    url: url+'/home/loadlistworker/'+start_date+'/'+end_date+'/'+id_user+'/'+id_sede+'/'+type+'/'+time+'/'+proceso,
                    type: 'GET',
                    // dataType: 'json',
                    beforeSend:function(response){
                        // $('#result').DataTable().destroy();
                        if(dataTableL){
                            $('#trabajadores').DataTable().destroy();
                            dataTableL = null;
                        }
                        $('#response-workers').children().remove( );
                        $('#response-workers').prepend("<tr><td><h2>Cargando datos...</h2></td></tr>");
                    },
                    success:function(response){
                        // console.log(response);
                        $('#response-workers').children().remove( );
                        $('#response-workers').html(response);
                        $('.nav-item').removeClass('active');
                        $('#n-2').addClass('active');
                        $('#carouselExampleFade').carousel(1);
                        // test();
                        // console.log(response);
                        // if(response.success){
                        //     updatePieData2(response.data,response.labels);
                        // }else{
                        //     $('#total-type').text("0");
                        //     // $('#total-asistencia').text("0");
                        //     removePieData2();
                        // }

                    },
                    complete:function(response){
                        console.log(response);
                        if(!dataTableL){
                            dataTableL = $('#trabajadores').DataTable({
                                // responsive: true
                                fixedHeader: false,
                                "pageLength": 50,
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                                // "order": [[ 7, "asc" ]]
                            });
                            dataTableL.fixedHeader.disable();
                            // new $.fn.dataTable.FixedHeader( dataTable );
                        }

                    },
                    error:function(response){
                        console.log(response);
                    }
            });
            //go to slide div
    }

    function listWorkersbyArea(id_area,type){
        var id_sede = $('#sede').val();
        var proceso = $('#cultivo').val();
            $.ajax({
                    url: url+'/home/loadlistworkerbyArea/'+start_date+'/'+end_date+'/'+id_area+'/'+id_sede+'/'+type+'/'+time+'/'+proceso,
                    type: 'GET',
                    // dataType: 'json',
                    beforeSend:function(response){
                        // $('#result').DataTable().destroy();
                        if(dataTableL){
                            $('#trabajadores').DataTable().destroy();
                            dataTableL = null;
                        }
                        $('#response-workers').children().remove();
                        $('#response-workers').prepend("<h2>Cargando datos...</h2>");
                    },
                    success:function(response){
                        // console.log(response);
                        $('#response-workers').children().remove( );
                        $('#response-workers').html(response);
                        $('.nav-item').removeClass('active');
                        $('#n-2').addClass('active');
                        $('#carouselExampleFade').carousel(1);
                        // test();
                        // console.log(response);
                        // if(response.success){
                        //     updatePieData2(response.data,response.labels);
                        // }else{
                        //     $('#total-type').text("0");
                        //     // $('#total-asistencia').text("0");
                        //     removePieData2();
                        // }

                    },
                    complete:function(response){
                        console.log(response);
                        if(!dataTableL){
                            dataTableL = $('#trabajadores').DataTable({
                                // responsive: true
                                fixedHeader: false,
                                "pageLength": 50,
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                                // "order": [[ 7, "asc" ]]
                            });
                            dataTableL.fixedHeader.disable();
                            // new $.fn.dataTable.FixedHeader( dataTable );
                        }

                    }
            });
    }

    // $('#sede').on('change',function(){
    //     console.log($(this).val());
    //     $('input[name="sedes"]').val($(this).val());
    // });

    $('#area').on('change',function(){
        console.log($(this).val());
        $('input[name="areas"]').val($(this).val());
    });

    //we need to change this function
    function search(){
        //range max for query 7 days
        var codigo = $('#codeOrdni').val();
        var sede = $('#sede').val();
        var checked_ = $('#switch_check').is(':checked') ? 1 : 0;
        var area = $('#area').val();
        var turno = $('#turno').val();
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
            data: {codigo:codigo,start_date:start_date,end_date:end_date,sede:sede,checked:checked_,area:area,turno:turno,proceso:proceso},
            beforeSend:function(response){
                // $('#result').DataTable().destroy();
                if(dataTable){
                    $('#result').DataTable().destroy();
                    dataTable = null;
                }
                // $('#result').children().remove( );
                $('#result').prepend("<h2>Cargando datos...</h2>");
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
                        fixedHeader: false,
                        "pageLength": 100,
                        "order": [[ 7, "asc" ]]
                    });
                    dataTable.fixedHeader.disable();
                    // new $.fn.dataTable.FixedHeader( dataTable );
                }

            }
        })

        // $('#response-table').DataTable();

    }

    function setTime(day){
        time = day;
        user = 0;
        checked = 0;
        loadGraphics(start_date,end_date);
        $('.nav-item').removeClass('active');
        $('#n-1').addClass('active');
        $('#carouselExampleFade').carousel(0);
        // test();
    }

</script>
@endsection



