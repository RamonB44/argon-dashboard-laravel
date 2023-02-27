
        <thead>
            <tr>
                {{-- <td>Ver detalles</td> --}}
                @foreach ($headings as $item)
                <td>{{ $item }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php

                $fecha1 = Carbon\Carbon::parse($start_date);
                $fecha2 = Carbon\Carbon::parse($end_date);
                $diff = $fecha1->diff($fecha2);
                $cont_checked = 0;
                $cont_unchecked = 0;
                $registros = 0;
                // dd($salida);
                // echo $salida;
            @endphp
            @foreach ($items as $value)
            @php
                $registros++;
            @endphp
            <tr>
                {{-- <td></td> --}}
                <td>{{ $value['Employe_ID'] }}</td>
                <td>{{ $value['Turno'] }}</td>
                <td>{{ $value['Sede'] }}</td>
                <td>{{ $value['Tipo'] }}</td>
                <td>{{ $value['Codigo'] }}</td>
                <td>{{ $value['Area'] }}</td>
                <td>{{ $value['Funcion'] }}</td>
                <td>{{ $value['Documento'] }}</td>
                <td>{{ $value['Nombres'] }}</td>
                <td>{{ $value['CCosto'] }}</td>
                @for ($i = 0; $i < $diff->days+ 1; $i++)
                    @php
                        $fecha1 = Carbon\Carbon::parse($start_date);
                        $current = $fecha1->addDays($i);

                        if($turno==0 || $turno == "DIA" || $turno == "NOCHE"){
                            $asistencia = App\Models\Asistencia::whereDate('created_at', '=',$current->format('Y-m-d'))
                            ->where('id_employe','=',$value['Employe_ID'])
                            ->when($salida,function ($query){
                                return $query->where('id_aux_treg',10);
                            })
                            ->get();
                            if(count($asistencia)<=0){
                                $asistencia = App\Models\Asistencia::where('id_employe','=',$value['Employe_ID'])
                                ->when($salida,function ($query){
                                    return $query->where('id_aux_treg',10);
                                }, function ($query) use ($current){
                                    return $query->whereNull('created_at')
                                    ->whereDate('deleted_at', '=',$current->format('Y-m-d'));
                                })
                                ->get();
                            }
                        }
                        if($turno == "S/T"){
                            $asistencia = App\Models\Asistencia::where('id_employe','=',$value['Employe_ID'])
                            ->when($salida,function ($query){
                                return $query->where('id_aux_treg',10);
                            }, function ($query) use ($current){
                                return $query->whereNull('created_at')
                                ->whereDate('deleted_at', '=',$current->format('Y-m-d'));
                            })
                            ->get();
                        }

                    @endphp
                    <td>
                        <div>
                            @forelse($asistencia as $v)
                            @php
                            $corregir = false;
                            @endphp
                            <p>
                                <span>
                                    @if($v->aux_type->description == "PERMISO" || $v->aux_type->description == "PERMISO_C" || $v->aux_type->description == "ASISTENCIA")
                                        <?php
                                            // $horas = $ingreso->diffinHours($salida);
                                            if($v->created_at && $v->deleted_at){
                                                $entry = \Carbon\Carbon::parse($v->created_at);// hora de ingreso 18:00:00
                                                $beat = \Carbon\Carbon::parse($v->deleted_at);// hora de salida 02:00:00

                                                $mins = $entry->diffInMinutes($beat, true);
                                                // $horas = $ingreso->diffinHours($salida);
                                                $horas = $mins/60;
                                                if($horas > 17){
                                                    $corregir = true;
                                                }
                                            }
                                            if($v->created_at && !$v->deleted_at){
                                                $corregir = true;
                                            }
                                            if(!$v->created_at && $v->deleted_at){
                                                $corregir = true;
                                            }
                                        ?>
                                        @if($v->created_at)
                                        {{-- <span class="badge badge-dark">{{ $v->c_costo }}</span> --}}
                                        <span class="badge badge-info" style="background-color: {{ $v->aux_type->color }}">[HI {{ Carbon\Carbon::parse($v->created_at)->format('H:i:s') }}]</span>
                                        @endif
                                        @if ($v->deleted_at)
                                        <span class="badge badge-secondary" style="background-color: #572364">[HS {{ Carbon\Carbon::parse($v->deleted_at)->format('H:i:s') }}]</span>
                                        @endif
                                        @if($corregir===true)
                                        <span class="badge badge-warning"><a style="color: white" href="javascript:fixed({{ $v->id }})">[ Observado ]</a></span>
                                        @endif
                                    @else
                                        <span class="badge badge-default text-white" style="background-color: {{ $v->aux_type->color }}" >{{ $v->aux_type->description }}</span>
                                    @endif
                                </span>
                                <!--@if($v->turno)-->
                                <!--<span class="badge badge-warning">-->
                                <!--    {{ $v->turno }}-->
                                <!--</span>-->
                                <!--@endif-->
                                <!--<span class="badge badge-primary" >{{ $v->sede->name }}</span>-->
                                @if($v->checked && $v->id_aux_treg == 1)
                                    <span class="badge badge-info">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    @php
                                        $cont_checked++;
                                    @endphp
                                @elseif(!$v->checked && $v->id_aux_treg == 1)
                                    @php
                                        $cont_unchecked++;
                                    @endphp
                                @endif
                            </p>
                            @empty
                                <p>
                                    <span class="badge badge-danger">Sin registros</span>
                                </p>
                            @endforelse
                        </div>
                    </td>
                @endfor
                <td>{{ $value['neto'] }}</td>
            </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr >
                <td colspan="{{ count($headings) -2 }}"> </td>
                <td>
                    <p>Desde : {{ $fecha1->format('Y/m/d') }}</p>
                    <p>Hasta : {{ $fecha2->format('Y/m/d') }} </p>
                </td>
                <td class="text-center">
                    <p >Registros: {{ $registros }}</p>
                    <p>Verificados: {{ $cont_checked }}</p>
                    <p>S/Verificar: {{ $cont_unchecked }}</p>
                    <p>Por Contratar: {{App\Models\Asistencia::whereNull('id_employe')->whereBetween(\DB::raw('UNIX_TIMESTAMP(DATE_FORMAT(created_at, "%Y-%m-%d %H:%i"))'), [strtotime($fecha1->format('Y-m-d H:i')), strtotime($fecha2->addHours(23)->addMinutes(59)->format('Y-m-d H:i'))])
                    ->select('*')->count() }}</p>
                </td>
            </tr>
        </tfoot>
