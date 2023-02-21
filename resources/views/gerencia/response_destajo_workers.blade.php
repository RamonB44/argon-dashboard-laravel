{{-- <table class="table table-hover"> --}}
    <thead>
        <tr>
            <th>#</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Modulo</th>
            <th>C.Costo</th>
            <th>Codigo</th>
            <th>Nombres</th>
            <th>Cantidad</th>
            <th>T.Pago. Neto</th>
            <th>T.Pago Bruto</th>
            <td>Horas Proceso</td>
            <td>Horas Marcacion</td>
            <td>Bio. Ingreso</td>
            <td>Bio. Salida</td>
            <td>Inicio. Proceso</td>
            <td>Fin. Proceso</td>
            {{-- <th></th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $k => $item)
            <tr>
                <td>{{ $k+1 }}</td>
                <td>{{ $item->assist }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->modulo }}</td>
                <td>{{ $item->c_costo }}</td>
                <td>{{ $item->sap_code }}</td>
                <td>{{ $item->fullname }}</td>
                <td>{{ round($item->total,3) }}</td>
                <td>{{ round($item->t_pago,3) }}</td>
                <td>{{ round($item->t_pago_bruto,3) }}</td>
                {{-- <td>{{ $item-> }}</td> --}}
                <td>{{ $item->real_hours }}</td>
                <td>{{ $item->biometric_hours }}</td>
                <td>{{ $item->dial_attendance_start }}</td>
                <td>{{ $item->dial_attendance_beat }}</td>
                <td>{{ $item->dial_destajo_start }}</td>
                <td>{{ $item->dial_destajo_end }}</td>
            </tr>
            {{-- <tr id="detail_{{ $item->sap_code }}">
               @forelse (\DB::connection('destajo_mysql')->table('reg_a') as $i)
                   
               @empty
                   
               @endforelse 
            </tr> --}}
        @empty
            
        @endforelse
        @forelse ($without as $k => $item)
        <tr>
            <td>{{ $data->count('sap_code') + $k + 1 }}</td>
            <td>{{ $item->assist }}</td>
            <td>{{ $item->created_at }}</td>
            <td>{{ $item->modulo }}</td>
            <td>{{ $item->c_costo }}</td>
            <td>{{ $item->sap_code }}</td>
            <td>{{ $item->fullname }}</td>
            <td>{{ round($item->total,3) }}</td>
            <td>{{ round($item->t_pago,3) }}</td>
            <td>{{ round($item->t_pago_bruto,3) }}</td>
            <td>{{ $item->real_hours }}</td>
            <td>{{ "-" }}</td>
            <td></td>
            <td></td>
            <td>{{ $item->dial_destajo_start }}</td>
            <td>{{ $item->dial_destajo_end }}</td>
        </tr>
        @empty
            
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7"></td>
            <td>{{ $data->sum('total') + $without->sum('total') }}</td>
            <td>{{ $data->sum('t_pago') + $without->sum('t_pago') }}</td>
            <td>{{ $data->sum('t_pago_bruto') + $without->sum('t_pago_bruto') }}</td>
            <td colspan="6"></td>
        </tr>
    </tfoot>
{{-- </table> --}}