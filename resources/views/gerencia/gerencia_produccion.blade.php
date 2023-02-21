    @extends('layouts.app-2')

    @section('title', 'Gerencia RR.HH')

    @section('content_header')
        <h1 class="m-0 text-dark">Reporte Produccion</h1>
    @stop

    @section('content')
    <div class="container">
        <div id="carouselExampleFade" class="carousel slide carousel-fade row" data-interval="false" data-ride="carousel">

            <div class="carousel-item active">
                <div class="row justify-content-between mb-2">
                    @php
                        $config = Auth::user()->getConfig();
                    @endphp
                    @if(count($config['sedes']) > 0)
                    <div class="col-md bd-highlight">
                        {{-- <small>Sedes</small> --}}
                        <select name="sedes" id="sedes" class="form-control">
                            {{-- <option value="0">Todas mis sedes</option> --}}
                            @foreach (App\Sedes::all() as $item)
                                @if(in_array($item->id,$config['sedes']))
                                    <option value="{{ $item->id }}" >SEDE PLANTA {{ $item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md bd-highlight mb-2">
                        <select class="form-control" name="cultivo" id="cultivo">
                            {{-- <option value="ALL">Todas mis procesos</option> --}}
                            @foreach (\App\Procesos::whereIn('id', \DB::table('areas_sedes')->whereIn('id_sede',$config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray() )->get() as $item)
                                    <option value="{{ $item->id }}" >{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md bd-highlight">
                        <!--<small>Turno</small>-->
                        <select class="form-control" name="turno" id="turno">
    
                          <option value="0">Todos</option>
                          @php
                                $time =Carbon\ Carbon::now()->timezone('America/Lima');
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
                    <div class="col-md bd-highlight">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch_check">
                                <label class="custom-control-label" for="switch_check">Costos</label>
                        </div>
                    </div>
                    <div class="col-md bd-highlight">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch_usd">
                                <label id="usd-value" class="custom-control-label" for="switch_usd">USD</label>
                        </div>
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
                {{-- <div id="response_jefes" class="row text-white text-center mb-3" style="border-bottom: 1px solid rgb(93, 60, 129);border-top: 1px solid rgb(93, 60, 129)">
                    <div class="dropdown col-md" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                        <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545; " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu Item 1
                        </a>

                        <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Sub Menu Item</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>

                    <div class="dropdown col-md" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                        <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu Item 1
                        </a>

                        <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Sub Menu Item</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>

                    <div class="dropdown col-md" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                        <a href="#" class="disabled" id="dropdownMenuLink" style="color:#89b545;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu Item 1
                        </a>

                        <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Sub Menu Item</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>

                    <div class="dropdown col-md" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                        <a href="#" class="disabled" id="dropdownMenuLink" style="color: #89b545" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu Item 4
                        </a>

                        <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Sub Menu Item</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>

                    <div class="dropdown col-md" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
                        <a href="#" class="disabled" id="dropdownMenuLink" style="color:#89b545;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu Item 5
                        </a>

                        <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="#">Sub Menu Item</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <!-- /.col (LEFT) -->
                    <div class="col-md-12" style="overflow-y: auto">
                        <!-- BAR CHART -->
                        <div class="card">
                            <div class="card-body" >
                                <div class="chart" >
                                    <div class="chartjs-size-monitor">
                                        <div class="chartjs-size-monitor-expand">
                                            <div class="">
                                            </div>
                                        </div>
                                        <div class="chartjs-size-monitor-shrink">
                                            <div class="">
                                            </div>
                                        </div>
                                        <p id="total" class="text-bold"
                                            style="width: 100%; height: 40px;font-size: 4.5vw; position: absolute; top: 50%; left: 0; margin-top: -10px; line-height:50px; text-align: center; z-index: 999999999999999">
                                        0
                                        </p>
                                    </div>
                                    <canvas id="pieChart" style="height: 500px; min-height: 230px; display: block; width: 500px;" width="487" height="230" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                            <!-- /.card -->
                    </div>
                    <!-- /.col (RIGHT) -->
                </div>
                <div id="response-details" class="row" style="border-top: 0.5px solid rgb(93, 60, 129);overflow-x:auto">
                    @php
                            // \DB::connection('destajo_mysql')->getPdo();
                            // DB::connection('destajo_mysql')->table('package_process')
                            // ->leftjoin(env('DB_DATABASE', '').'.employes','package_process.sap_code',env('DB_DATABASE', '').'.employes.code')
                            // ->leftjoin(env('DB_DATABASE', '').'.reg_assistance',env('DB_DATABASE', '').'.employes.id',env('DB_DATABASE', '').'.reg_assistance.id_employe')
                            // ->where('package_process.sede_id',1)// sede_id from package_process                        ->where(env('DB_DATABASE', '').'.reg_assistance.id_aux_treg',1)
                            // ->where(env('DB_DATABASE', '').'.reg_assistance.id_sede',9)//sede_id from assistance
                            // ->whereNotNull(env('DB_DATABASE', '').'.reg_assistance.created_at')
                            // ->whereDate('package_process.created_at',\Carbon\Carbon::today()->subDay(3)->toDateString())
                            // ->whereDate(env('DB_DATABASE', '').'.reg_assistance.created_at',\Carbon\Carbon::today()->subDay(3)->toDateString())
                            // ->select('package_process.*',env('DB_DATABASE', '').'.reg_assistance.created_at as created_at_ass')
                            // ->groupBy('package_process.sap_code')
                            // ->get();

                            // $reg_assistance = DB::table('reg_assistance')
                            // ->where('id_aux_treg',1)
                            // ->whereDate('created_at',\Carbon\Carbon::today()->subDay(3)->toDateString())
                            // ->get();
                            // $reg_package = DB::connection('destajo_mysql')->table('package_process')
                            // ->whereDate('package_process.created_at',\Carbon\Carbon::today()->subDay(3)->toDateString())
                            // ->get();
                            // dd($reg_packages);
                    @endphp
                </div>
            </div>
            <div class="carousel-item">
                <div class="row mb-2">
                    <div class="col-md text-center">
                        <strong>Costos por trabajador</strong>
                    </div>
                    <div class="col-md">
                        <a class="btn btn-outline-primary btn-lg btn-block" data-target="#carouselExampleFade" data-slide-to="0">Regresar</a>
                    </div>
                </div>
                <div class="table mb-2" style="overflow-x:auto">

                        <table id="response-workers" class="display" style="border-top: 0.5px solid rgb(93, 60, 129);width:100%">
                        
                        </table>
                </div>
                <div class="row">
                    <div class="col-md text-center">
                        <strong>Costos por trabajador</strong>
                    </div>
                    <div class="col-md">
                        <a class="btn btn-outline-primary btn-lg btn-block" data-target="#carouselExampleFade" data-slide-to="0">Regresar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/green/pace-theme-flash.min.css" />
    <!--<link href="https://unpkg.com/bootstrap-table@1.17.1/dist/extensions/group-by-v2/bootstrap-table-group-by.css" rel="stylesheet">-->
    <!--<link href="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.css" rel="stylesheet">-->
    @endsection

    @section('js')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
    <script>
        var pieData = null;
        var start_date = "{{ date('Y-m-d') }}";
        var end_date = "{{ date('Y-m-d') }}";
        var url = "{{ url('/') }}";
        var dataTable = null;
        var exchangeRate = 0;
        var total = 0;
        var total_usd = 0;
        var montos = [];

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

        $(document).ready((e)=>{
            pieData = {
                labels: ['RECEPCION','SELECCIÓN','EMPAQUE','PRE PACK','EMBOLSADO','CÁMARA','CALIDAD','SANEAMIENTO','ALMACÉN','MANTENIMIENTO','INF. DE PRODUCCIÓN','PRODUCCIÓN','VIGILANCIA','TRANSPORTE','RECURSOS HUMANOS'],
                datasets: [
                    {
                    data: [5,200,400,600,300,100,700,200,400,600,300,100,600,300,100],
                    backgroundColor : ['blue', 'red', 'orange', 'black', 'yellow', 'violet', 'pink', 'bluelight',getRandomColor(),getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor()],
                    }
                ],
            }
            loadGraphics(start_date,end_date);
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
                $('#daterange-btn span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'))
                start_date = start.format('YYYY-MM-DD');
                end_date = end.format('YYYY-MM-DD');
                // loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
                reloadAll();
            }
        );

        $('#sedes').change(function(e){
            loadGraphics(start_date,end_date);
        });

        $('#cultivo').change(function(e){
            loadGraphics(start_date,end_date);
        });
        
        $('#switch_usd' ).on( "click", function(){
            if($('#switch_usd').is(':checked')){
                if($('#switch_check').is(':checked')){
                    changeExchangeValue()
                    //cambiar montos dolares
                    // #('.usd_pen').text();
                    $(".usd_pen").children().each((i,e)=>{
                        // ...
                        // console.log($(e).text());
                        // $(e).text('USD');
                        var monto_inicial = $(e).text().replace( /^\D+/g, '');
                        // console.log(monto_inicial);
                        montos[i] = monto_inicial;
                        var monto = monto_inicial / exchangeRate;
                        // console.log(monto);
                        $(e).text("USD "+monto.toFixed(2));
                        // $('.usd_pen').text($(this).text() * exchangeRate);
                    });
                    //reload chart to usd;
                    updateChartUSDPEN("USD");

                }else{
                    // $('#sku').val("");
                    // loadGraphics(start_date,end_date);
                    $(this).prop("checked", false);
                }
            }else{
                $(".usd_pen").children().each((i,e)=>{
                        // ...
                        // $(e).text('S/.');
                        $(e).text("S/."+montos[i]);
                        // $('.usd_pen').text($(this).text() * exchangeRate);
                });
                //regresar a normallidad chart
                updateChartUSDPEN("PEN");
                $('#total').text(total);
            }
        });

        $('#switch_check' ).on( "click", function(){
            if($('#switch_check').is(':checked')){
                loadGraphics(start_date,end_date);
            }else{
                // $('#sku').val("");
                loadGraphics(start_date,end_date);

            }
            $('#switch_usd').prop('checked',false);
        });

        function loadUSD(){
            return new Promise((resolve,reject)=>{
                $.ajax({
                    url: "http://www.floatrates.com/daily/pen.json",
                    type: "get",
                    dataType: "json",
                    success:function(response){
                        // console.log(response.usd.rate);
                        exchangeRate = response.usd.inverseRate;
                        $('#usd-value').html('USD: '+exchangeRate.toFixed(2));
                        resolve(exchangeRate);
                    },
                    error: function(response){
                        reject(0)
                        console.log(response);
                    }
                });
            });
        }

        async function loadGraphics(start,end){
            $('#switch_usd').prop("checked", false);
            var sedes = $('#sedes').val();
            var is_costo = $('#switch_check').is(':checked') ? 1 : 0;
            var proceso = $('#cultivo').val();
            var turno = $('#turno').val();
            exchangeRate = await loadUSD();
            $.ajax({
                url: url+'/home/loadDestajo/'+sedes+'/'+proceso+'/'+start+'/'+end+'/'+is_costo+'/'+turno,
                type: 'get',
                dataType: 'json',
                error:function(e){
                    console.log(e);
                },
                success:function(response){
                        if(response.success){
                            total = response.total;
                            $('#total').text(response.total);
                            $('#response-details').html(response.reg_packages);
                            updatePieData(response.data,response.labels,response.backgroundcolor,response.hidden);
                        }else{
                            $('#total').text(0);
                            $('#response-details').html("")
                            removePieData();
                        }

                },
                error:function(e){
                    console.log(e);
                },
                complete:function(e){
                    console.log(e);
                }
            });
            // console.log(exchangeRate);
        }

        function reloadAll(){
            loadGraphics(start_date,end_date);
        }

        function updateChartUSDPEN(mode){
            // pieChart.data.labels = label;
            // var max = data.length;
            // pieChart.data.labels = [];
            // var lenght = pieChart.data.datasets.data.length;
            console.log(mode);
            console.log(pieChart.data.datasets[0].data);
            var nuevos = [];

            if(mode=="USD"){
                pieChart.data.datasets[0].data.forEach((e,i) => {
                //
                console.log(e);
                    //to usd
                    // ;
                    var valor = e / exchangeRate;
                    nuevos[i] = valor.toFixed(2);
                // dataset.backgroundColor = [];
                });

            }else{
                pieChart.data.datasets[0].data.forEach((e,i) => {
                //
                    var valor = e * exchangeRate;
                    nuevos[i] = valor.toFixed(2);
                        //to usd
                        // dataset.data = dataset.data * exchangeRate;
                    // dataset.backgroundColor = [];
                });
                    //to pen
            }
            console.log(pieChart.data.datasets[0].data);
            pieChart.data.datasets.forEach((e,i) => {
                //
                    console.log(e);
                    e.data = nuevos;
                    //to usd
                    // ;
                    // nuevos[i] = e / exchangeRate;
                // dataset.backgroundColor = [];
                });
            pieChart.update();
        }

        function updatePieData(data,label,backgroundColor,hidden) {
            pieChart.data.labels = label;
            var max = data.length;
            var dataset = [];
            // console.log(backgroundColor);
            // var color = ['blue', 'red', 'orange', 'black', 'yellow', 'violet', 'pink', '#4f8ad1','brown','green', 'grey', '#ad325b', '#8aad32'];
            pieChart.data.datasets = [];

            dataset = {
                    backgroundColor: backgroundColor,
                    data: data
            }

            pieChart.data.datasets.push(dataset);

            pieChart.update();

            $(hidden).each(function(i,e){

                if(e){
                    // console.log(e + "| n"+i);
                    pieChart.getDatasetMeta(0).data[i].hidden = e;
                }
            });
            contador = 0;
            pieChart.update();
            // console.log(pieChart.getDatasetMeta);

        }

        function removePieData() {

            pieChart.data.labels = [];
            pieChart.data.datasets.forEach((dataset) => {
            //
                dataset.data = [];
                dataset.backgroundColor = [];
            });
            pieChart.update();

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

            var pieChartOptions = {
                title: {
                    display: true,
                    text: 'COSTOS POR AREA'
                },
                legend: {
                            display: false,
                            onClick: (e) => e.stopPropagation(),
                            "labels": {
                                "fontSize": 15,
                            },
                            position: "top",
                            align: "start"
                        },
                maintainAspectRatio : false,
                responsive: true,
                cutoutPercentage: 80,
                tooltips: {
                    enabled: true,
                    caretPadding: 20,
                },
                showAllTooltips: false
            }

            pieChart = new Chart(pieChartCanvas3, {
                type: 'pie',
                data: pieData,
                options: pieChartOptions
            })
        });

        function loadDestajoMenbers(function_id,area_id){
            var sede_id = $('#sedes').val();
            var proceso_id = $('#cultivo').val();
            $.ajax({
                url: url+'/home/loadDestajoMembers/'+sede_id+'/'+proceso_id+'/'+area_id+'/'+function_id+'/'+start_date+'/'+end_date,
                dataType: 'json',
                type: 'get',
                beforeSend: function(e){
                    if(dataTable){
                        $('#response-workers').DataTable().destroy();
                        dataTable = null;
                    }
                },
                success: function(e){
                    $('#response-workers').children().remove();
                    $('#response-workers').append(e.table);
                    $('#carouselExampleFade').carousel(1);
                },
                error: function(e){
                    console.log(e);
                },
                complete: function(e){
                    if(!dataTable){
                        dataTable = $('#response-workers').DataTable({
                                    orderCellsTop: true,
                            fixedHeader: true,
                            bLengthChange: false,
                            bVisible: false,
                            dom: 'Bfrtip',
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ],
                            fixedHeader: false,
                            "pageLength": 100,
                            // "order": [[ 1, "asc" ]]
                        });
                        dataTable.fixedHeader.disable();
                        // new $.fn.dataTable.FixedHeader( dataTable );
                    }
                }
            });
        }

        function changeExchangeValue (){
            var total = $('#total').text();
            total = total.replace( /^\D+/g, '');
            console.log(total);
            console.log(exchangeRate.toFixed(3));
            var usd = total / exchangeRate.toFixed(3);

            $('#total').text("USD "+usd.toFixed(2));
            console.log(usd);
        }
    </script>
    @endsection