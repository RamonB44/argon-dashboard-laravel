<div class="table">
    <table class="table table-hover text-center">
        <thead class="dir">
            <th><a href="javascript:void(0)" style="color:rgb(93, 60, 129)"><i class="fas fa-book"></i>
                <strong>Areas</strong></a>
            </th>
            @if(Auth::user()->hasGroupPermission('viewRGerencia'))
                <th >
                    <a href="javascript:void(0)" style="color: blue;"><i class="fas fa-chart-area"></i>
                    Jornal</a>
                </th>
                <th >
                    <a href="javascript:void(0)" style="color: red;"><i class="fas fa-chart-area"></i>
                    Destajo</a>
                </th>
                <th style="border-left: 1px solid rgb(93, 60, 129)">
                    <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                    Directos</a>
                </th>
                <th>
                    <a href="javascript:void(0)" style="color: green;"><i class="fas fa-chart-area"></i>
                    Indirectos</a>
                </th>
                <th>
                    <a href="javascript:void(0)" ><i class="fas fa-book"></i>
                    Verificados</a>
                </th>
                <th>
                    <a href="javascript:void(0)"><i class="fas fa-book"></i>
                    Sin Verificar</a>
                </th>


            @elseif(Auth::user()->hasGroupPermission('viewRGerenciaRecursos'))
                <th >
                    <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                    Total</a>
                </th>
                <th>
                    <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                        KPI</a>
                </th>
                <th >
                    <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                    Porcentaje</a>
                </th>
            @endif
        </thead>
        <tbody >
            @if(Auth::user()->hasGroupPermission('viewRGerencia'))
                @php
                    $total = 0;
                    $total2 = 0;
                    $total_v = 0;
                    $total_sv = 0;
                @endphp
                @forelse($datos as $k => $value)
                <tr >
                    <td style="color: rgb(93, 60, 129)" ><strong>{{ $value->name }}</strong></td>
                    <td style="color: blue">{{ $value->jornal }}</td>
                    <td style="color: red">{{ $value->destajo }}</td>


                    <td style="color: rgb(93, 60, 129);border-left:1px solid rgb(93, 60, 129)">{{ $value->directo }}</td>
                    <td style="color: green">{{ $value->indirecto }}</td>
                    <td><a href="javascript:listWorkersbyArea({{ $value->id }},'V')">{{ $value->verificados }}</a></td>
                    <td><a href="javascript:listWorkersbyArea({{ $value->id }},'SV')">{{ $value->no_verificados }}</a></td>
                </tr>
                @php
                    $total += ($value->destajo + $value->jornal);
                    $total2 += ($value->directo + $value->indirecto);
                @endphp
                @empty
                <tr>
                    <td colspan="5" ><h1>No existen datos</h1></td>
                </tr>
                @endforelse
            @elseif(Auth::user()->hasGroupPermission('viewRGerenciaRecursos'))
                @forelse($datos as $k => $value)
                <tr class="dir" style="color: {{ $value->color }}" onclick="loadSummaryWorkers({{ $value->id_area }},{{ $sede }})">
                    <td><strong>{{ $value->name }}</strong></td>
                    <td class="usd_pen"><strong>
                        <?php if($checked){echo "S/.";} ?>
                        {{ round($value->total,2) }}</strong></td>
                        @php
                            $kpi1 = $checked ? round(($value->total/ $multiplicador) / $value->total_qty,2) : round(($value->total_money/ $multiplicador) / $value->total,2);
                            // $color1 = $kpi1 > 22 ? "red": "green";
                            $icono1 = $kpi1 > 22 ? "danger": "success";
                        @endphp
                        <td>{{ $kpi1 }}
                            <div class="spinner-grow text-{{ $icono1 }}" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </td>
                    <td><strong>{{ round( $value->total * 100 / ( $datos->sum('total')  + $datos2->sum('total') ) ) }}%</strong></td>
                </tr>
                @empty
                <!--<tr>-->
                <!--    <td colspan="5" ><h1>No existen datos</h1></td>-->
                <!--</tr>-->
                @endforelse
                @if(count($datos2) > 0)
                <tr class="dir text-center" style="color:orange;" onclick="javascript:showBackOffice()">
                    <td><strong>INDIRECTOS</strong></td>
                    <td class="usd_pen"><strong>
                        <?php if($checked){echo "S/.";} ?>
                        {{ round($datos2->sum('total'),2) }}</strong></td>
                        @php
                            $kpi2 = $checked ? round(($datos2->sum('total')/ $multiplicador) / $datos2->sum('total_qty'),2) : round(($datos2->sum('total_money') / $multiplicador) / $datos2->sum('total'),2);
                            $icono2 = $kpi2 > 22 ? "danger" : "success";
                        @endphp
                        <td>{{ $kpi2 }}
                            <div class="spinner-grow text-{{ $icono2 }}" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </td>
                    <td><strong>{{ round( $datos2->sum('total') * 100 / ( $datos->sum('total')  + $datos2->sum('total') ) ) }}%</strong></td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" >
                        <div id="backoffice" class="row" style="display:none">
                            <div class="col-md-12">
                                <a class="btn btn-outline-primary btn-lg btn-block" onclick="javascript:showBackOffice()">Regresar</a>
                            </div>

                            <div class="col-md-12">
                                <table class="table" style="width: 100%">
                                <thead>
                                    <th style="width: 37%"><a href="javascript:void(0)" style="color:rgb(93, 60, 129)"><i class="fas fa-book"></i>
                                        <strong>Areas</strong></a>
                                    </th>
                                    <th style="width: 13.5%">
                                        <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                                        Total</a>
                                    </th>
                                    <th style="width: 13.5%">
                                        <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                                        KPI</a>
                                    </th>
                                    <th style="width: 33%">
                                        <a href="javascript:void(0)" style="color: rgb(93, 60, 129);"><i class="fas fa-chart-area"></i>
                                        Porcentaje</a>
                                    </th>
                                </thead>
                                <tbody>
                                    @forelse($datos2 as $k => $v)
                                    <tr style="color: {{ $v->color }}" onclick="loadSummaryWorkers({{ $v->id_area }},{{ $sede }})">
                                        <td><strong>{{ $v->name }}</strong></td>
                                        <td class="usd_pen"><strong>
                                            <?php if($checked){echo "S/.";} ?>
                                            {{ round($v->total,2) }}</strong></td>
                                        @php
                                            $kpi3 = $checked ? round(($v->total / $multiplicador )/ $v->total_qty,2) : round(($v->total_money / $multiplicador) / $v->total,2);
                                            $icono3 = $kpi3 > 22 ? "danger" : "success";
                                        @endphp
                                        <td>{{ $kpi3 }}
                                            <div class="spinner-grow text-{{ $icono3 }}" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </td>
                                        <td><strong>{{ round(($v->total * 100) / $datos2->sum('total'),2) }}%</strong></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3">No existen datos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>
                        </div>

                    </td>
                </tr>

                <!--<tr>-->
                <!--    <td colspan="5" ><h1>No existen datos</h1></td>-->
                <!--</tr>-->

            @endif
        </tbody>
        @if(isset($total))
        <tfoot>
            <tr>
               <td></td>
               <td colspan="2" class="text-dark" style="background: rgb(126,167,247);background: linear-gradient(90deg, rgba(126,167,247,1) 0%, rgba(207,89,89,1) 100%);font-weight:bold">TOTAL DESTAJO Y JORNAL: {{ $total }}</td>
               <td colspan="2" class="text-dark" style="background: rgb(159,113,245);background: linear-gradient(90deg, rgba(159,113,245,1) 0%, rgba(150,255,180,1) 100%);font-weight:bold">TOTAL DIRECTO Y INDIRECTO : {{ $total2 }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
