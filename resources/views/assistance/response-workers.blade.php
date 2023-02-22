@if($type=="SM")
    @forelse ($data as $item)
    <tr>
        <td>{{ $item->code }}</td>
        <td>{{ $item->fullname }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->dir_ind }}</td>
        <td>{{ $item->area }}</td>
        <td>{{ $item->funcion }}</td>
        <td>{{ $item->c_costo }}</td>
        <td>{{ (empty($item->user) ? "NO VERIFICADO AUN":$item->user->name) }}</td>
        <td><a class="btn btn-warning disabled" href="javascript:editar({{ $item->id }})">Editar</a></td>
    </tr>
    @empty
    <td colspan="9" class="text-center">NO SE ENCONTRARON DATOS</td>
    @endforelse

@elseif($type=="SR")

    @forelse ($data as $item)
    <tr>
        <td>{{ $item->code }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ "S/N" }}</td>
        <td>{{ (empty($item->user) ? "NO VERIFICADO AUN":$item->user->name) }}</td>
        <td> - </td>
    </tr>
    @empty
    <td colspan="9" class="text-center">NO SE ENCONTRARON DATOS</td>
    @endforelse

@else
    @forelse ($data as $item)
    <tr>
        <td>{{ $item->employes->code }}</td>
        <td>{{ $item->employes->fullname }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->dir_ind }}</td>
        <td>{{ $item->funcion->areas->area }}</td>
        <td>{{ $item->funcion->description }}</td>
        <td>{{ $item->c_costo }}</td>
        <td>{{ (empty($item->user) ? "NO VERIFICADO AUN":$item->user->name) }}</td>
        <td><a class="btn btn-warning" href="javascript:editar({{ $item->id }})">Editar</a></td>
    </tr>
    @empty
    <td colspan="9" class="text-center">NO SE ENCONTRARON DATOS</td>
    @endforelse
@endif