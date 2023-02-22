@forelse ($users as $item)
<div class="col mb-2">
    <div class="card text-white bg-success">
      {{-- <img src="..." class="card-img-top" alt="..."> --}}
      <div class="card-body align-item-center">
        <h6 class="card-title font-weight-bold text-center">{{ $item->name }}</h6>
        <div class="list-group">
          <a href="javascript:listWorker({{ $item->id }},'V')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            VERIFICADOS
            <span class="badge badge-success">{{ \App\Asistencia::whereIn('id_function',json_decode($item->user_group[0]->pivot->show_function))->where('id_sede',$sede)->where('id_user_checked',$item->id)->where('id_aux_treg',1)->whereDate('created_at',\Carbon\Carbon::parse($date)->toDateString())->count() }}</span>
          </a>
          <a href="javascript:listWorker({{ $item->id }},'SV')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            SIN VERIFICAR
            <span class="badge badge-warning">{{ \App\Asistencia::whereIn('id_function',json_decode($item->user_group[0]->pivot->show_function))->where('id_sede',$sede)->where('id_aux_treg',1)->where('checked',0)->whereDate('created_at',\Carbon\Carbon::parse($date)->toDateString())->count() }}</span>
          </a>
          <a href="javascript:listWorker({{ $item->id }},'SM')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            SIN ASISTENCIA
            <span class="badge badge-danger">{{ \DB::table('reg_unchecked')
                ->join( 'employes' ,function ($join){
                    $join->on('reg_unchecked.reg_code','=','employes.code');
                    $join->orOn('reg_unchecked.reg_code','=','employes.doc_num');
                })
                ->join('funct_area','employes.id_function','funct_area.id')
                ->join('areas','funct_area.id_area','areas.id')
                ->where('reg_unchecked.id_user',$item->id)
                ->whereDate('reg_unchecked.created_at',\Carbon\Carbon::parse($date)->toDateString())
                ->select('employes.*','funct_area.description as funcion','areas.area as area')
                ->count() }}</span>
          </a>
          <a href="javascript:listWorker({{ $item->id }},'I')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              INCORRECTOS
            <span class="badge badge-danger">{{ \App\Asistencia::whereNotIn('id_function',json_decode($item->user_group[0]->pivot->show_function))->where('id_sede',$sede)->where('id_aux_treg',1)->where('id_user_checked',$item->id)->whereDate('created_at',\Carbon\Carbon::parse($date)->toDateString())->count() }}</span>
          </a>
            <a href="javascript:listWorker({{ $item->id }},'SR')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            SIN REGISTRO
            <span class="badge badge-danger">{{ \DB::table('reg_employes')->where('id_user',$item->id)->whereDate('created_at',\Carbon\Carbon::parse($date)->toDateString())->count() }}</span>
            </a>
        </div>
      </div>
    </div>
</div>
@empty
    
@endforelse