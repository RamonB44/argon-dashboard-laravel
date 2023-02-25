@extends('layouts.app-2')

@section('title', 'Gerencia General')

@section('content_header')
    <h1 class="m-0 text-dark">Reporte Gerencia General</h1>
@stop

@section('content')
<div class="container">

    <div id="eleccion" class="row d-flex justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card mb-3" style="max-width: 540px;">
              <div class="row no-gutters">
                <div class="col-md-4">
                  <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcRmuBKXZOdU4VA5QW0XVa7VqSeaMH1R8YYoEA&usqp=CAU" class="card-img" alt="...">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h5 class="card-title text-center text-bold">Planta</h5>
                    <!--<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>-->
                    <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                  </div>
                  <div class="card-footer">
                      <a class="btn btn-outline-success btn-lg btn-block" href="javascript:void(null)">VER</a>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3" style="max-width: 540px;">
              <div class="row no-gutters">
                <div class="col-md-4">
                  <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcRmuBKXZOdU4VA5QW0XVa7VqSeaMH1R8YYoEA&usqp=CAU" class="card-img" alt="...">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h5 class="card-title text-center text-bold">Fundo</h5>
                    <!--<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>-->
                    <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                  </div>
                  <div class="card-footer">
                      <a class="btn btn-outline-success btn-lg btn-block" href="javascript:void(null)">VER</a>
                  </div>
              </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div id="resultado" class="d-flex justify-content-center">

            <figure class="highcharts-figure">
                <div id="chart"></div>
           </figure>

        </div>
    </div> --}}

</div>
@endsection
@section('css')
<style>

.highcharts-figure, .highcharts-data-table table {
    min-width: 320px;
    width: 500px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>
@endsection
@section('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/sunburst.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>

</script>
<script>
// script for sunburst graphics
function loadSunBurst(dom_element,data){
    data = [{
    name: 'Planta ',
    id: 'id-1'
},{
    name: 'Sur',
    parent: 'id-1',
    value: 2
}, {
    name: 'Norte',
    parent: 'id-1',
    value: 1
}]

    // Splice in transparent for the center circle
    Highcharts.getOptions().colors.splice(0, 0, "transparent");

    Highcharts.chart(dom_element, {
        chart: {
            height: '100%',
            // width: '100%'
        },
        title: {
            text: 'Asistencias Planta',
        },
        subtitle: {
            text: 'Grafico de previsualizacion de Asistencias.'
        },
        series: [{
            type: "sunburst",
            data: data,
            allowDrillToNode: true,
            cursor: 'pointer',
            dataLabels: {
                format: '{point.name}',
                filter: {
                    property: 'innerArcLength',
                    operator: '>',
                    value: 120
                },
                rotationMode: 'circular'
            },
            levels: [{
                level: 1,
                levelIsConstant: false,
                dataLabels: {
                    filter: {
                        property: 'outerArcLength',
                        operator: '>',
                        value: 64
                    }

                }
            }, {
                level: 2,
                colorByPoint: true,
                visible: false
            },
            {
                level: 3,
                colorVariation: {
                    key: 'brightness',
                    to: -0.5
                }
            }, {
                level: 4,
                colorVariation: {
                    key: 'brightness',
                    to: 0.5
                }
            }]

        }],
        tooltip: {
            headerFormat: "",
            pointFormat: 'La Asistencia total de <b>{point.name}</b> es <b>{point.value}</b>'
        }
    });
    //generate sunburst charts
}

</script>
@endsection
