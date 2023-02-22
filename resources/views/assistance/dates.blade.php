@php
    $year = \Carbon\Carbon::now()->year;
    //print("año: ". $year);
    $now = \Carbon\Carbon::now();
    $today = \Carbon\Carbon::today();
    $current_date = $now->setISODate($year,$week_number)->setHours(0)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');
    $startofweek = \Carbon\Carbon::parse($current_date)->startOfWeek();
    $endofweek = \Carbon\Carbon::parse($current_date)->endOfWeek();
    //print("inicio".$startofweek);
    //print("final:".$endofweek);
    $days = $startofweek->diffInDays($endofweek);
    //print("differencia: ". $days);
@endphp

<div class="btn-group mr-2" role="group" aria-label="First group">
    @for ($i = 0; $i < $days + 1; $i++)
        @php
            $current = \Carbon\Carbon::parse($current_date)->startOfWeek()->addDay($i);
        @endphp
        <a href="javascript:changeDate('{{ $current->format('Y-m-d') }}')" type="button" <?php  if($current->gt($today)){echo 'class="btn btn-success disabled" role="button" aria-disabled="true"';}else{echo 'class="btn btn-success"';} ?>>{{ $current->translatedFormat('l/d') }}</a>
    @endfor
</div>

