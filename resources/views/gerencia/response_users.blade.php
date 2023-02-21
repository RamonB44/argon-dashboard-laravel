@foreach ($users as $value)
    @php
    $areas = json_decode($value->user_group[0]->pivot->show_areas);
    $funciones = json_decode($value->user_group[0]->pivot->show_function);
    /*$datos = App\Asistencia::whereHas('funcion',function($query) use ($areas){
                $query->whereIn('id_area',$areas);
            })->where('id_sede','=',$sede)->where('id_aux_treg','=',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
            ->select(DB::raw('count(*) as asistencias'),DB::raw('sum(case when checked = 1 then 1 else 0 end) as verificados'))->first();*/
    $datos = App\Asistencia::whereIn('id_function',$funciones)
    ->where('id_aux_treg','=',1)
    ->where('id_sede','=',$sede)
    ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
    ->whereNull('deletedAt')
    ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
    ->select(DB::raw('count(*) as asistencias'),DB::raw('coalesce(sum(case when checked = 1 then 1 else 0 end),0) as verificados'))
    ->first();

    $ss = App\Asistencia::whereIn('id_function',$funciones)
    ->where('id_aux_treg','=',1)
    ->where('id_sede','=',$sede)
    ->whereIn('turno',(empty($time) ? ["DIA","NOCHE"]:[$time]))
    ->whereNull('deleted_at')
    ->whereNull('deletedAt')
    ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
    ->count();

    $unchecked = \DB::table('reg_unchecked')
    ->where('id_user','=',$value->id)
    ->whereNull('deleted_at')
    ->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->format('Y-m-d H:i'))])
    ->count();
    @endphp

<div class="dropdown col-md py-3" style="border-bottom: 0.1px double rgb(93, 60, 129);border-top: 0.1px double rgb(93, 60, 129)">
    <a onclick="javascript:reloadChart({{ $value->id }})" href="javascript:reloadChart({{ $value->id }})" class="disabled" id="dropdownMenuLink" style="color: #89b545; " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ $value->name }} : {{ $datos->asistencias }}
    </a>

    <div class="dropdown-menu" style="width: inherit !important;" aria-labelledby="dropdownMenuLink">
        {{-- verificados --}}
        <a class="dropdown-item" href="javascript:listWorkers({{ $value->id }},'V')">VERIFICADOS : {{ $datos->verificados }}</a>
        {{-- sin verificar --}}
        <a class="dropdown-item" href="javascript:listWorkers({{ $value->id }},'SM')">F/ASISTENCIA : {{ $unchecked }}</a>
        @if(Carbon\Carbon::today()->toDateString() != $fecha1->toDateString())
        <a class="dropdown-item" href="javascript:listWorkers({{ $value->id }},'SS')">F/SALIDA: {{ $ss }}</a>
        @endif
        {{-- asistidos --}}
        <a class="dropdown-item">TOTAL : {{ $unchecked + $datos->verificados}}</a>
    </div>
</div>
@endforeach
