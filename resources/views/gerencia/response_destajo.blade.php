<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Funcion</th>
            <th>Total</th>
            <th>T.pago</th>
            <th>KPI</th>
            <th>Porcentaje</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($reg_packages as $k => $item)
            <tr style="background-color: {{ $item->background_color }}" class="text-white" onclick="loadDestajoMenbers({{ $item->funcion_id }},{{ $item->area_id }})">
                <td>{{ $k+1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ round($item->t_workers,2) }}</td>
                <td class="usd_pen">{{ round($item->net_paid) }}</td>
                <td>{{ round($item->net_paid / $item->t_workers,2) }}</td>
                <td>{{ round(($item->t_workers * 100) /  $reg_packages->sum('t_workers'),2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" colspan="text-center">
                    Praise the sun!!!
                </td>
            </tr>
        @endforelse
    </tbody>
</table>