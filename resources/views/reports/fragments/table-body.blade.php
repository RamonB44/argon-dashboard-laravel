
    <thead>
        <tr>
            @php
                $elemento = array();
            @endphp
            @forelse ($headings as $k => $item)
                @if (is_array($item))
                    @php
                       $elemento[] = array_values($item);
                    @endphp
                    <th colspan="{{ count($item) }}">{{ $k }}</th>
                @else
                    <th rowspan="2">{{ $item }}</th>
                @endif
            @empty
                
            @endforelse
        </tr>
        <tr>

            @foreach ($elemento as $q => $i)
                @foreach ($i as $a)
                    <th>{{ $a }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>

    <tbody>
        @forelse ($sub as $s)
        <tr>
            @foreach ($s as $q => $sa)
                    
                    @if(str_contains($q, '_OBS'))
                    <td>
                       @if ($sa)
                        <a href="{{ $sa }}">Revisar</a>
                       @endif
                    </td>
                    @else
                    <td>
                        {{ $sa }}
                     </td>
                    @endif
                    
            @endforeach
        </tr>
        @empty
            
        @endforelse
    </tbody>
