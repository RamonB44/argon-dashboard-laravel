    <thead>
        <tr>
            <td class="">#</td>
            <td>T.Trabajador</td>
            <td class="">Codigo</td>
            <td class="">Nombres</td>
            <td>Funcion</td>
            <td>CCosto</td>
            <td>Basico</td>
            <td class="">Costos</td>
            @if($days == 1)
            <td>Horas Aprox.</td>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
        $x = 1;
        @endphp
        @foreach($datos as $key => $value)
            <tr>
                <td>{{ $x++ }}</td>
                <td>{{ $value->type }}</td>
                <td>{{ $value->employes->code }}</td>
                <td>{{ $value->employes->fullname }}</td>
                <td>{{ $value->funcion->description }}</td>
                <td>{{ empty($value->employes->c_costo) ? "NO DEFINIDO" : $value->employes->c_costo }}</td>
                <td class="text-right">
                <?php if($multiplicador==1){echo "S/.";}else{echo "USD";} ?>
                    {{ round($value->employes->remuneracion / $multiplicador,2) }}</td>
                <td class="text-right">
                <?php if($multiplicador==1){echo "S/.";}else{echo "USD";} ?>
                    {{ round($value->costos / $multiplicador,2) }}</td>
                @if($days == 1)
                    @php
                        //$descuento_horas = Asistencia::parse($value->created_at);
                        $ingreso = \Carbon\Carbon::parse($value->created_at);// hora de ingreso 18:00:00
                        $salida = empty($value->deleted_at) ? \Carbon\Carbon::parse($value->created_at)->addHours(8) : \Carbon\Carbon::parse($value->deleted_at);// hora de salida 02:00:00
                        $mins = $ingreso->diffInMinutes($salida, true);
                        // $horas = $ingreso->diffinHours($salida);
                        $mins = $mins > 480 ? $mins - 60 : $mins;
                        //$horas = $mins/60;
                        //$horas = $horas > 8 ? $horas - 1 : $horas;
                        $hours = date('H:i', mktime(0,$mins));
                        //$minutes = $final_time_saving % 60;
                        //modificar diferecia horaria, adicionar minutos
                        //$horas = (empty($value->deleted_at) ? "8:00:00" :  ($value->created_at->diffInHours($value->deleted_at) > 8 ? $value->created_at->diffInHours($value->deleted_at) - 1 : $value->created_at->diffInHours($value->deleted_at)).":00:00" );
                    @endphp
                    <td>{{ $hours }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-center">Total</td>
            <td class="text-right">
                <?php if($multiplicador==1){echo "S/.";}else{echo "USD";} ?>
                {{ round($datos->sum('costos') / $multiplicador,2) }}</td>
        </tr>
    </tfoot>