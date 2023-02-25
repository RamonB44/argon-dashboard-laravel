@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Asistencia Masiva'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md">
                                <h2 class="text-center text-bold">Reporte de Asistencia</h2>
                            </div>
                            {{-- limitar 7 dias --}}
                            <div class="bd-highlight col-md" data-step="3" data-intro="el filtro por rango de fechas.">
                                <small>Filtro por fechas</small>
                                <button type="button" class="btn btn-default m-1 btn-sm btn-block" id="daterange-btn">
                                    <span style="">
                                        <i class="fa fa-calendar"></i> {{ date('Y-m-d') . ' - ' . date('Y-m-d') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                            {{-- <div class="col-md">
                            <small>Filtro por semanas</small>
                            <input class="form-control" type="number" name="week_number" id="week_number" value="{{ \Carbon\Carbon::now()->weekOfYear }}">
                        </div> --}}
                        </div>
                        <div class="row">
                            @php
                                $config = Auth::user()->getConfig();
                            @endphp
                            @if (count($config['sedes']) > 0)
                                <div class="bd-highlight col-md-2">
                                    <small>Sedes</small>
                                    <select name="sede" id="sede" class="form-control">
                                        <option value="0" selected>Todas mis sedes</option>

                                        @foreach (App\Models\Sedes::all() as $item)
                                            @if (in_array($item->id, $config['sedes']))
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="bd-highlight col-md-2">
                                <small>Mis Procesos</small>
                                <select class="form-control" name="cultivo" id="cultivo">
                                    <option value="ALL">Todos mis procesos</option>
                                    @foreach (\App\Models\Procesos::whereIn('id',\DB::table('areas_sedes')->whereIn('id_sede', $config['sedes'])->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray())->get() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bd-highlight col-md">
                                <small>Areas</small>
                                <select name="area" id="area" class="form-control">
                                    <option value="0" selected>Todas las areas</option>
                                    @foreach (App\Models\Area::all() as $item)
                                        @if (in_array($item->id, $config['areas']))
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
                        </div>
                        <div class="row mt-2">
                            {{-- <div class="bd-highlight col-md" >
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
                            </div> --}}
                            <div class="bd-highlight col-md">
                                <form action="{{ route('xlsx.getAssisXlsxRRHH') }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="start" name="start" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" id="end" name="end" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" id="sedes" name="sedes" value="0">
                                    <input type="hidden" id="areas" name="areas" value="0">
                                    <input type="hidden" id="turnos" name="turnos" value="0">
                                    <button type="submit" class="btn btn-success m-1 btn-sm btn-block w-100">Exportar
                                        [RR.HH]</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <div class="table table-responsive">
                            <table id="result" class="display" style="width:100%">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('js')
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        var url = '{{ url('/') }}';
        var start_date = "{{ date('Y-m-d') }}";
        var end_date = "{{ date('Y-m-d') }}";
        var dataTable = null;
        $(document).ready(function(e) {
            $('#daterange-btn').daterangepicker({
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
                    ranges: {
                        'Hoy': [moment(), moment()],
                        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                        'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                        'Este Semana': [moment().startOf('week').day('1'), moment().endOf('week').day('7')],
                        'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                        'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment(),
                    endDate: moment(),
                },
                function(start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                        'MMMM D, YYYY'))
                    //loadGraphics(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
                    start_date = start.format('YYYY-MM-DD');
                    end_date = end.format('YYYY-MM-DD');
                    $('input[name="start"]').val(start_date);
                    $('input[name="end"]').val(end_date);
                    search();
                }
            );

            search();
        });

        function search() {
            //range max for query 7 days
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
                url: url + '/assistance/newsearch',
                type: 'POST',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    sede: sede,
                    checked: checked,
                    area: area,
                    turno: turno,
                    salida: salida,
                    proceso: proceso
                },
                beforeSend: function(response) {
                    // $('#result').DataTable().destroy();
                    if (dataTable) {
                        $('#result').DataTable().destroy();
                        dataTable = null;
                    }
                },
                error: function(response) {
                    console.log(response);
                },
                success: function(response) {
                    console.log(response)
                    $('#result').children().remove();
                    $('#result').append(response);
                    // console.log()

                },
                complete: function(response) {
                    console.log(response);
                    if (!dataTable) {
                        dataTable = $('#result').DataTable({
                            // responsive: true
                            orderCellsTop: true,
                            fixedHeader: true,
                            bLengthChange: true,
                            bVisible: true,
                            dom: 'Blfrtip',
                            pageLength: 50,
                            lengthMenu: [
                                [50, 100, 150, 200],
                                [50, 100, 200, 'Todos']
                            ],
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
    </script>
@endsection
