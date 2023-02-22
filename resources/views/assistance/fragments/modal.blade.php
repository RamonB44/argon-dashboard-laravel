<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
    <h4 class="modal-title">Registrar Asistencia</h4>
</div>
<form id="formAssistance" method="post">
    {{ csrf_field() }}
    <div class="modal-body">
        <div class="row">
            <div class="col-md">
                <small>Desde la fecha</small>
               <input type="date" name="d_since_at" value="{{ date('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md">
                <small>Hasta la fecha</small>
                <input type="date" name="d_until_at" value="{{ date('Y-m-d') }}" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <small>Desde la hora [24 hrs]</small>
               <input type="time" name="h_since_at" value="08:00" class="form-control">
            </div>
            <div class="col-md">
                <small>Hasta la hora [24 hrs]</small>
                <input type="time" name="h_until_at" value="17:00" class="form-control">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" >Guardar</button>
        <button type="button" class="btn btn-default" id="btnclosemodal" data-dismiss="modal">Cerrar</button>
    </div>
</form>