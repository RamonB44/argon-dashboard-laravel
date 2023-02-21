@php
    $c = 1;
    $x = 1;
@endphp
    @if($type == "V")
    
        @forelse($workers_checked as $k => $v)
            <tr>
                <td>{{ $c++ }}</td>
                <td>{{ $v->employes->code }}</td>
                <td>{{ $v->employes->fullname }}</td>
                <td>{{ $v->funcion->description }}</td>
                <td>{{ $v->checked_at }}</td>
            </tr>
        @empty
            <tr>
                <td>No se encontraron trabajadores</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
            </tr>
        @endforelse
    @elseif($type=="SM")
        @forelse($workers_checked_sm as $k => $v)
            <tr>
                <td>{{ $c++ }}</td>
                <td>{{ $v->reg_code }}</td>
                <td>{{ $v->message }}</td>
                <td>{{ "-" }} </td>
                <td>{{ $v->created_at }}</td>
            </tr>
        @empty
                <tr>
                    <td>No se encontraron trabajadores</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
        @endforelse
    @elseif($type=="SR")
    
        @forelse ($workers_sr as $k => $v)
        <tr>
            <td>{{ $c++ }}</td>
            <td>{{ $v->code }}</td>
            <td>{{ "S/N" }}</td>
            <td>{{ "S/N" }}</td>
            <td>{{ $v->created_at }}</td>
        </tr>
        @empty
                <tr>
                    <td>No se encontraron trabajadores</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
        @endforelse
    
    @elseif($type=="SV")
            @forelse($workers_sv as $k => $v)
                <tr>
                    <td>{{ $c++ }}</td>
                    <td>{{ $v->employes->code }}</td>
                    <td>{{ $v->employes->fullname }}</td>
                    <td>{{ $v->funcion->areas->area }}</td>
                    <td>{{ $v->checked_at }}</td>
                </tr>
            @empty
                <tr>
                                        <td>No se encontraron trabajadores</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
    @elseif($type=="SS")
            @forelse($workers_ss as $k => $v)
                <tr>
                    <td>{{ $c++ }}</td>
                    <td>{{ $v->employes->code }}</td>
                    <td>{{ $v->employes->fullname }}</td>
                    <td>{{ $v->funcion->areas->area }}</td>
                    <td>{{ $v->created_at }}</td>
                </tr>
            @empty
                <tr>
                                        <td>No se encontraron trabajadores</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
    @elseif($type==0)
        @forelse($workers_checked as $k => $v)
            <tr>
                <td>{{ $c++ }}</td>
                <td>{{ $v->code }}</td>
                <td>{{ $v->fullname }}</td>
                <td>{{ $v->funcion->areas->area }}</td>
                <td>{{ $v->checked_at }}</td>
            </tr>
        @empty
        @endforelse
        
        @forelse($workers_checked_sm as $k => $v)
            <tr>
                <td>{{ $x++ }}</td>
                <td>{{ $v->code }}</td>
                <td> - </td>
                <td> - </td>
                <td>{{ $v->created_at }}</td>
            </tr>
        @empty
        @endforelse
    @endif