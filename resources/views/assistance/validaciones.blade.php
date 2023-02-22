@extends('layouts.app')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Gestionar Asistencia {{ date('Y-m-d') }}</h1>
@stop


@section('content')
<div class="container">
    <div class="row d-flex justify-content-center mb-2" >
        @php
        $config = Auth::user()->getConfig();
        $main_sede = $config['sedes'][0];
        //print($main_sede); = 1
        $data = \DB::table('areas_sedes')->where('id_sede',$main_sede)->select('id_proceso')->distinct('id_proceso')->get()->pluck('id_proceso')->toArray();
        //dd($data);
        @endphp
        <div class="col-md-1 mb-2">
            <!--<label>Numero de semana</label>-->
            <small>N° Sem.</small>
            <input class="form-control" type="number" name="week_number" id="week_number" value="{{ \Carbon\Carbon::now()->weekOfYear }}"> 
        </div>
        <div hidden class="col-md-4 mb-2">
            <small>Mis Procesos</small>
            <select class="form-control" name="cultivo" id="cultivo">
                @foreach ( \App\Procesos::whereIn('id', $data )->get() as $item)
                        <option value="{{ $item->id }}" >{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <small>Mis Sedes</small>
            <select class="form-control" name="sede" id="sede">
                <!--<option value="0">TODAS MIS SEDES</option>-->
                @foreach (\App\Sedes::all() as $item)
                    @if(in_array($item->id,$config['sedes']))
                        <option value="{{ $item->id }}" >{{ $item->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
            @php
                $startofweek = \Carbon\Carbon::today()->startOfWeek();
                $endofweek = \Carbon\Carbon::today()->endOfWeek();
                $today = \Carbon\Carbon::today();
                    if($today->dayOfWeek ===  \Carbon\Carbon::MONDAY){
                        $startofweek = \Carbon\Carbon::today()->subWeek()->startOfWeek();
                        $endofweek = \Carbon\Carbon::today()->subWeek()->endOfWeek();
                    }
                $days = $startofweek->diffInDays($endofweek);
            @endphp
            <div class="btn-group mr-2" role="group" id="week_dates" aria-label="First group">
                @for ($i = 0; $i < $days + 1; $i++)
                    @php
                        if($today->dayOfWeek ===  \Carbon\Carbon::MONDAY){
                        $current = \Carbon\Carbon::today()->subWeek()->startOfWeek()->addDay($i);
                        }else{
                        $current = \Carbon\Carbon::today()->startOfWeek()->addDay($i);
                        }
                        
                    @endphp
                    <a href="javascript:changeDate('{{ $current->format('Y-m-d') }}')" type="button" <?php  if($current->gt($today)){echo 'class="btn btn-success disabled" role="button" aria-disabled="true"';}else{echo 'class="btn btn-success"';} ?>>{{ $current->translatedFormat('l/d') }}</a>
                @endfor
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Third group">
              <a href="javascript:changeDate('{{ $today->format('Y-m-d') }}')" type="button" class="btn btn-info">Hoy</a>
            </div>
        </div>
        
    </div>
    <div id="users-data" class="row row-cols-1 row-cols-md-4  d-flex justify-content-around mb-2">
        @forelse ($users as $item)
        <div class="col mb-2">
            <div class="card text-white bg-success">
              {{-- <img src="..." class="card-img-top" alt="..."> --}}
              <div class="card-body align-item-center">
                <h6 class="card-title font-weight-bold text-center">{{ $item->name }}</h6>
                <div class="list-group">
                  <a href="javascript:listWorker({{ $item->id }},'V')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    VERIFICADOS
                    <span class="badge badge-success">{{ \App\Asistencia::whereIn('id_function',json_decode($item->user_group[0]->pivot->show_function))
                    ->where('id_user_checked',$item->id)
                    ->where('id_aux_treg',1)
                    ->where('id_sede',$config['sedes'][0])
                    ->whereDate('created_at',\Carbon\Carbon::today()->toDateString())
                    ->count() }}</span>
                  </a>
                  <a href="javascript:listWorker({{ $item->id }},'SV')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    SIN VERIFICAR
                    <span class="badge badge-warning">{{ \App\Asistencia::whereIn('id_function',json_decode($item->user_group[0]->pivot->show_function))
                    ->where('id_aux_treg',1)
                    ->where('checked',0)
                    ->where('id_sede',$config['sedes'][0])
                    ->whereDate('created_at',\Carbon\Carbon::today()->toDateString())->count() }}</span>
                  </a>
                  <a href="javascript:listWorker({{ $item->id }},'SM')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    SIN ASISTENCIA
                    <span class="badge badge-danger">{{ \DB::table('reg_unchecked')
                        ->join('employes','reg_unchecked.reg_code','employes.code')
                        ->join('funct_area','employes.id_function','funct_area.id')
                        ->join('areas','funct_area.id_area','areas.id')
                        ->where('reg_unchecked.id_user',$item->id)
                        ->whereDate('reg_unchecked.created_at',\Carbon\Carbon::today()->toDateString())
                        ->select('employes.*','funct_area.description as funcion','areas.area as area')
                        ->count() }}</span>
                  </a>
                  <a href="javascript:listWorker({{ $item->id }},'I')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      INCORRECTOS
                    <span class="badge badge-danger">{{ \App\Asistencia::whereNotIn('id_function',json_decode($item->user_group[0]->pivot->show_function))->where('id_aux_treg',1)->where('id_user_checked',$item->id)->whereDate('created_at',\Carbon\Carbon::today()->toDateString())->count() }}</span>
                  </a>
                  <a href="javascript:listWorker({{ $item->id }},'SR')" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      SIN REGISTRO
                    <span class="badge badge-danger">{{ \DB::table('reg_employes')->where('id_user',$item->id)->whereDate('created_at',Carbon\Carbon::today()->toDateString())->count() }}</span>
                  </a>
                </div>
              </div>
            </div>
        </div>
        @empty
            
        @endforelse
    </div>

    <div class="row table">
        <table id="workers" class="table table-bordered">
            <thead>
                <th>Codigo</th>
                <th>Nombres</th>
                <th>DES/JOR</th>
                <th>DIR/IND</th>
                <th>Area</th>
                <th>Funcion</th>
                <th>CCosto</th>
                <th>Verificador</th>
                <th>Acciones</th>
            </thead>
            <tbody id="result-workers">
                <td colspan="9" class="text-center"><h2>Esperando interaccion...</h2></td>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
          <h4 class="modal-title">Editar Registro</h4>
        </div>
        <form id="formupdateRegister" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <small>Descripcion</small>
                        <select disabled name="description" id="description_edit" class="form-control">
                            @foreach (App\Model\Auxiliar\TypeReg::all() as $item)
                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <small>Desde la fecha</small>
                       <input disabled type="date" name="d_since_at" id="d_since_at_edit" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <small>Hasta la fecha</small>
                        <input disabled type="date" name="d_until_at" id="d_until_at_edit"  value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <small>Desde la hora [24 hrs]</small>
                       <input type="time" name="h_since_at" id="h_since_at_edit" value="08:00" class="form-control" disabled>
                    </div>
                    <div class="col-md-4">
                        <small>Hasta la hora [24 hrs]</small>
                        <input type="time" name="h_until_at" id="h_until_at_edit" value="17:00" class="form-control" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <small>Proceso</small>
                        <select name="proceso" id="proceso-edit" class="form-control" onchange="loadAreaEdit()">
                            @foreach (\App\Procesos::all() as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <small>DES/JOR</small>
                        <select name="des_jor" id="des_jor_edit" class="form-control" required>
                            <option value="DESTAJO">DESTAJO</option>
                            <option value="JORNAL">JORNAL</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <small>DIR/IND</small>
                        <select name="dir_ind" id="dir_ind_edit" class="form-control" required>
                            <option value="DIRECTO">DIRECTO</option>
                            <option value="INDIRECTO">INDIRECTO</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <small>Area</small>
                        <select name="area" id="area-edit" class="form-control" onchange="loadFuncionEdit()" required>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <small>Funcion</small>
                        <select name="area" id="funcion-edit" class="form-control" required>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <small>CCosto</small>
                        <select name="area" id="c_costo-edit" class="form-control" required>

                        </select>
                    </div>
                    <div class="col-md-2">
                        <br>
                        <div class="custom-control custom-switch mt-2 d-flex justify-content-center">
                            <input type="checkbox" class="custom-control-input" id="switch_check">
                            <label class="custom-control-label" for="switch_check">Permanente</label>
                        </div>
                    </div>
                    
                </div>
            </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" >Guardar</button>
        </form>
        <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/green/pace-theme-flash.min.css" />
@endsection
@section('js')

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/af-2.3.5/b-1.6.2/b-colvis-1.6.2/b-flash-1.6.2/b-html5-1.6.2/b-print-1.6.2/cr-1.5.2/fc-3.3.1/fh-3.1.7/kt-2.5.2/r-2.2.5/rg-1.1.2/rr-1.2.7/sc-2.0.2/sp-1.1.1/sl-1.3.1/datatables.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script>
    var url = "{{ url('/') }}";
    var date = "{{ date('Y-m-d') }}";
    var dataTableL = null;
    
    function listWorker(id_user,type){
        //type SN then id_user is id_sede in backend
        var id_sede = $('#sede').val();
        var id_proceso = $('#cultivo').val();
        $.ajax({
            url: url + "/assistance/loadlistworker/"+date+"/"+id_user+"/"+type+"/"+id_sede+"/"+id_proceso,
            method: "get",
            datatype: "json",
            beforeSend: function(response){
                // console.log(dataTableL);
                if(dataTableL){
                    dataTableL.destroy();
                    dataTableL = null;
                }
                // $('#result-workers').children().remove();
                // $('#result-workers').html("<td colspan='6'>No se cargaron datos</td>");
                // $('#workers').
            },
            success: function(response){
                
                $('#result-workers').children().remove();
                $('#result-workers').html(response.response);
                // console.log(response.response)
            },
            complete:function(response){
                // console.log(dataTableL);
                if(!dataTableL){
                    dataTableL = $('#workers').DataTable({
                        // responsive: true
                        destroy: true,
                        retrieve: true,
                        fixedHeader: false,
                        "pageLength": 50,
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        // "order": [[ 7, "asc" ]]
                    });
                    dataTableL.fixedHeader.disable();
                    // new $.fn.dataTable.FixedHeader( dataTable );
                }

            },
            error:function(response){
                if(dataTableL){
                    dataTableL.destroy();
                    // dataTableL = null;
                }
                $('#result-workers').children().remove();
                $('#result-workers').html("<td colspan='6'>No se cargaron datos</td>");
            }
        });
    }

    function loadUsers(date) {
        var id_proceso = $('#cultivo').val();
        var id_sede = $('#sede').val();
        $.ajax({
            url: url + "/assistance/loadUsers/"+date+"/"+id_proceso+"/"+id_sede,
            method: "get",
            // datatype: "json",
            beforesuccess: function(response){
                $('#users-data').children().remove();
                $('#result-workers').children().remove();
                $('#result-workers').html("<td colspan='6'>Esperando interaccion</td>");
                // $('#workers').
            },
            success: function(response){
                $('#users-data').children().remove();
                $('#result-workers').children().remove();
                $('#result-workers').html("<td colspan='6'>Esperando interaccion</td>");
                $('#users-data').html(response);
                console.log(response)
            },
            error:function(response){
                $('#users-data').children().remove();
                $('#result-workers').children().remove();
                $('#result-workers').html("<td colspan='6'>Esperando interaccion</td>");
            }
        });
        
        //load no registers
        
        
    }

    function changeDate(newdate){
        date = newdate;
        // console.log(date);
        loadUsers(date);
    }
    
    function editar(id){
        console.log("editando registreo :"+id);
        $.ajax({
            url: url+"/manageassistance/editRegister/"+id,
            method: 'get',
            success: function(response){
                console.log(response);
                if(response.success){
                    showEdit(response,id);
                }
                // showMessageBox(response)
            },
            error: function(response){
                console.log(response);
            }
        })
    }

    async function showEdit(response,id){
        $('#description_edit').val(response.data.id_aux_treg);
        $('#d_since_at_edit').val(response.data.d_since_at);
        $('#d_until_at_edit').val(response.data.d_until_at);
        $('#h_since_at_edit').val(response.data.h_since_at);
        $('#h_until_at_edit').val(response.data.h_until_at);
        $('#des_jor_edit').val(response.data.type);
        $('#dir_ind_edit').val(response.data.dir_ind);
        $('#proceso-edit').val(response.data.id_proceso);
        $('#formupdateRegister').attr('action',url+"/manageassistance/editValidacion/"+id);
        const loadA = await loadAreaEdit();
        //must be caougth before functionloadAreaEdit and loadFunctionEdit
        if(loadA===true){
            // setTimeout((e)=>{
            $('#area-edit').val(response.data.funcion.areas.id);
            const loadF = await loadFuncionEdit();
            if(loadF===true){
                $('#funcion-edit').val(response.data.id_function);
                $('#c_costo-edit').val(response.data.c_costo);
            } 
            // },3000);
        }
        $('#editModal').modal('show');
    }

    function loadAreaEdit(){
        // debugger;
        return new Promise((resolve,reject)=>{
            var id_sede = $('#sede').val();
            var id_proceso = $('#proceso-edit').val();
            var elements = $('#area-edit').children(); //get all childrens elements : option

            $.ajax({
                url: url+'/areas/loadAreas/'+id_sede+'/'+id_proceso,
                method: 'get',
                dataType: 'json',
                success: function(response){
                    console.log(response);
                    $('#area-edit').children().remove();
                    $.each(response, function( index, value ) {
                        // alert( index + ": " + value );
                        $('#area-edit').append($('<option>').val(value.id).text(value.area));
                    });
                    resolve(true);
                },
                error: function(response){
                    console.log(response);
                    reject(response);
                }
            }); 
        });
        // loadFuncionEdit();
    }

    function loadFuncionEdit(){
        return new Promise((resolve,reject)=>{
            // setTimeout((e)=>{
                var id_area = $('#area-edit').val();
                var id_proceso = $('#proceso-edit').val();
                var id_sede = $('#sede').val();
                $.ajax({
                    url: url+'/funcion/loadByArea/'+id_area+'/'+id_sede+'/'+id_proceso,
                    method: 'get',
                    dataType: 'json',
                    success: function(response){
                        // console.log(response);
                        $('#funcion-edit').children().remove();
                        $.each(response.funcion, function( index, value ) {
                            // alert( index + ": " + value );
                            $('#funcion-edit').append($('<option>').val(value.id).text(value.description));
                        });
                        $('#c_costo-edit').children().remove();
                        // console.log(response.ccosto.ccosto);
                        $.each(JSON.parse(response.ccosto.ccosto), (i,v)=>{
                            $('#c_costo-edit').append($('<option>').val(v).text(v));
                        });
                        resolve(true);
                    },
                    error: function(response){
                        console.log(response);
                        reject(response)
                    }
                });
            // else put resolve
            // },1000);
        });
    }

    $('#formupdateRegister').submit(function(e){
        e.preventDefault();
        var datos = {};
        // datos['d_since_at'] = $('#d_since_at_edit').val();
        // datos['d_until_at'] = $('#d_until_at_edit').val();
        // datos['h_until_at'] = $('#h_until_at_edit').val();
        // datos['h_since_at'] = $('#h_since_at_edit').val();
        // datos['description'] = $('#description_edit').val();
        datos["id_proceso"] = $('#proceso-edit').val();
        datos["id_funcion"] = $('#funcion-edit').val();
        datos["dir_ind"] = $('#dir_ind_edit').val();
        datos["des_jor"] = $('#des_jor_edit').val();
        datos["c_costo"] = $('#c_costo-edit').val();
        console.log($('#switch_check').is(':checked'));
        datos["permanent"] = $('#switch_check').is(':checked') ? true: false;
        console.log(datos);
        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: datos,
            success: function(response){
                console.log(response);

                // updateCalendarData(response.calendardata);
                // clean();
                showMessageBox(response);
                $('#editModal').modal('hide');
            },
            error: function(response){
                console.log(response);
            }
        })

    });

    function showMessageBox(response){
        Swal.fire({
            // position: 'top-end',
            icon: response.icon,
            title: response.title,
            text: response.message,
            showConfirmButton: false,
            timer: 1500
        })
    }
    
    function loadWeek_Dates(week_number){
        // console.log(week_number);
        $.ajax({
            url: url+"/assistance/week_Dates/"+week_number,
            type: "get",
            success:function(res){
                console.log(res);
                $('#week_dates').children().remove();
                $('#week_dates').append(res);
            },
            error: function (ex){
                console.log(ex);
            }
        })
    }
    
    $('#week_number').keypress( (e) => {
        if(e.which == 13) {
            loadWeek_Dates($('#week_number').val());
        }
    });
</script>
@endsection