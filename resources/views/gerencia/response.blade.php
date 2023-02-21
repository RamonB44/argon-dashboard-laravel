
    <table class="table table-responsive text-center">
        <thead>
            <tr>
                @foreach ($users as $item)
                    <td colspan="3" id="{{ $item->id }}" onclick="javascript:reloadChart({{ $item->id }})" style="font-size: 1.5em;border: 2px solid black;" ><strong>{{ $item->name }}</strong></td>
                    <td></td>
                @endforeach
            </tr>
        </thead>
        <tbody >
            <tr>
                @foreach ($users as $value)
                    @php
                        $areas = json_decode($value->user_group[0]->pivot->show_areas);
                        /*$datos = App\Asistencia::whereHas('funcion',function($query) use ($areas){
                                    $query->whereIn('id_area',$areas);
                                })->where('id_sede','=',$sede)->where('id_aux_treg','=',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
                                ->select(DB::raw('count(*) as asistencias'),DB::raw('sum(case when checked = 1 then 1 else 0 end) as verificados'))->first();*/
                        $datos = App\Asistencia::where('id_user_checked','=',$value->id)->where('id_aux_treg','=',1)->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
                                ->select(DB::raw('count(*) as asistencias'),DB::raw('coalesce(sum(case when checked = 1 then 1 else 0 end),0) as verificados'))->first();
                        $unchecked = \DB::table('reg_unchecked')->whereBetween(DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])->where('id_user','=',$value->id)->count();
                    @endphp
                    <td id="td-{{ $value->id }}" onclick="javascript:reloadChart({{ $value->id }})" style="font-size: 1.1em;border: 2px solid black;">
                        V/A: {{ $datos->verificados }}
                    </td>
                    <td id="td2-{{ $value->id }}" onclick="javascript:reloadChart({{ $value->id }})" style="font-size: 1.1em;border: 2px solid black;">
                        S/A: {{ $unchecked }}
                    </td>
                    <td id="td3-{{ $value->id }}" onclick="javascript:reloadChart({{ $value->id }})" style="font-size: 1.1em;border: 2px solid black;">
                        T: {{ $unchecked + $datos->verificados}}
                    </td>
                    <td></td>
                @endforeach

                {{-- <td>160A - 52V</td> --}}
            </tr>
        </tbody>
    </table>

