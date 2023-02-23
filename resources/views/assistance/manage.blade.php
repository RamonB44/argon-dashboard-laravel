@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestion de Asistencia'])
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" data-step="1"
                        data-intro="Modulo de gestion de asistencia por empleado, en este modulo podras visualizar la asistencia del empleado y gestionarla de manera individual.">
                        <h2 id="manage-title">Gestion de Asistencias por Empleado</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2" data-step="2" data-intro="Introduce el codigo SAP del empleado.">
                                <small>Codigo de Empleado </small>
                                <input type="text" name="employe" id="employe" class="form-control">
                            </div>
                            <div class="col-md-10 d-flex justify-content-center">
                                <div id="message-employe" data-step="3" data-intro="El resultado se mostrara aqui.">

                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="result" class="container-fluid" data-step="4"
                            data-intro="en esta seccion podras gestionar y visualizar los registro de asistencia del empleado."
                            hidden>
                            <div class="row mb-2">
                                <div class="col-md">
                                    <a class="btn btn-success m-auto btn-block btn-sm w-100" href="javascript:void(null)"
                                        data-bs-toggle="modal" data-bs-target="#assistanceModal">Registrar Asistencia</a>
                                </div>
                                <div class="col-md">
                                    <a class="btn btn-primary m-auto btn-block btn-sm w-100" href="javascript:void(null)"
                                        data-bs-toggle="modal" data-bs-target="#permissionModal">Registrar Permisos</a>
                                </div>
                                <div class="col-md">
                                    <a class="btn btn-primary m-auto btn-block btn-sm w-100" href="javascript:void(null)"
                                        data-bs-toggle="modal" data-bs-target="#licenceModal">Añadir Licencias</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <a class="btn btn-warning m-auto btn-block btn-sm w-100" href="javascript:void(null)"
                                        data-bs-toggle="modal" data-bs-target="#vacationModal">Registrar Vacaciones</a>
                                </div>
                                <div class="col-md">
                                    <a class="btn btn-danger m-auto btn-block btn-sm w-100" href="javascript:void(null)"
                                        data-bs-toggle="modal" data-bs-target="#ceseModal">Cesar Trabajor</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="sticky-top mb-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Elementos</h4>
                                            </div>
                                            <div class="card-body">
                                                <!-- the events -->
                                                <div id="external-events">
                                                    <div class="external-event badge bg-gradient-success ui-draggable ui-draggable-handle"
                                                        style="position: relative;">Asistencia</div>
                                                    <div class="external-event badge bg-gradient-warning ui-draggable ui-draggable-handle"
                                                        style="position: relative;">Licencia</div>
                                                    <div class="external-event badge bg-gradient-info ui-draggable ui-draggable-handle"
                                                        style="position: relative;">Permiso</div>
                                                    <div class="external-event badge bg-gradient-primary ui-draggable ui-draggable-handle"
                                                        style="position: relative;">Libre</div>
                                                    <div class="external-event badge bg-gradient-danger ui-draggable ui-draggable-handle"
                                                        style="position: relative;">Falta</div>
                                                    <div class="checkbox">
                                                        <label for="drop-remove">
                                                            <input type="checkbox" id="drop-remove" hidden>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-md-9">
                                    <div class="card card-primary">
                                        <div class="card-body p-0">
                                            <!-- THE CALENDAR -->
                                            <div id="calendar" class="fc fc-ltr fc-bootstrap" data-step="6"
                                                data-intro="visualizar y editar los registros solo haciendo click en alguno de ellos."
                                                style="">

                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="assistanceModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Registrar Asistencia</h4>
                </div>
                <form id="formAssistance" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
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
                        <div class="row">
                            <div class="col-md">
                                <small>Descontar hora de almuerzo?</small>
                                <select name="hasDinner" id="hasDinner" class="form-control">
                                    <option value="1">Si</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        id="permissionModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Registrar Permiso <small class="text-danger">Requiere que en el dia registre
                            una asistencia</small></h4>
                </div>
                <form id="formPermission" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Fecha de salida</small>
                                <input type="date" name="since_date" value="{{ date('Y-m-d') }}" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <small>Hora de Salida [24Hrs]</small>
                                <input type="time" name="since_hour_at" value="08:00" class="form-control" required>
                            </div>
                            <div class="col-md">
                                <small>Hora de Retorno [24Hrs] <strong class="text-info">Dejar en blanco si el personal no
                                        retornara</strong></small>
                                <input type="time" name="since_hour_until" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        id="licenceModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Registrar Licencia <small class="text-danger">removera todos los registros que
                            esten dentro de la fecha. Tomando la fecha de inicio como referencia.</small></h4>
                </div>
                <form id="formLicence" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Descripcion</small>
                                <select name="description" id="description_" class="form-control">
                                    <option value="4">Licencia sin goce de haber</option>
                                    <option value="5">Licencia con goce de haber</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        id="vacationModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Registrar Vacaciones</h4>
                </div>
                <form id="formVacation" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md">
                                <small>Hasta la fecha <strong class="text-info">30 Dias Maximo</strong></small>
                                <input type="date" name="d_until_at" value="" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="ceseModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Registrar Cese</h4>
                </div>
                <form id="formCese" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la fecha <strong></strong></small>
                                <input type="date" name="d_since_at" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        id="removeModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true"></span></button>
                    <h4 class="modal-title">Remover registros - Rango de fechas</h4>
                </div>
                <form id="formRemove" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <small>Descripcion</small>
                                <select name="description" id="description_re" class="form-control">
                                    @foreach (App\Models\Auxiliar\TypeReg::whereIn('id', [1, 3, 4, 5, 7, 10])->get() as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" id="d_since_at_re" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" id="d_until_at_re" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btnSend">Guardar</button>
                </form>
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" id="editModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formupdateRegister" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Editar Registro</h4>
                        <a class="btn btn-danger" id="btn_delete" href="javascript:void(null)">Eliminar</a>
                    </div>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md">
                                <small>Descripcion</small>
                                <select name="description" id="description_re" class="form-control">
                                    @foreach (App\Models\Auxiliar\TypeReg::all() as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la fecha</small>
                                <input type="date" name="d_since_at" id="d_since_at_edit"
                                    value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                            <div class="col-md">
                                <small>Hasta la fecha</small>
                                <input type="date" name="d_until_at" id="d_until_at_edit"
                                    value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <small>Desde la hora [24 hrs]</small>
                                <input type="time" name="h_since_at" id="h_since_at_edit" value="08:00"
                                    class="form-control">
                            </div>
                            <div class="col-md">
                                <small>Hasta la hora [24 hrs]</small>
                                <input type="time" name="h_until_at" id="h_until_at_edit" value="17:00"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('css')
    <link href="{{ asset('assets/fullCalendarJs/fullcalendar/main.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fullCalendarJs/fullcalendar-daygrid/main.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fullCalendarJs/fullcalendar-bootstrap/main.min.css') }}" rel="stylesheet" />
@endsection

@section('js')
    <script src="{{ asset('assets/fullCalendarJs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar-daygrid/main.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar-timegrid/main.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar-interaction/main.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/fullcalendar-bootstrap/main.min.js') }}"></script>
    <script src="{{ asset('assets/fullCalendarJs/core/locales/es.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        var calendar = null;
        $(function() {

            /* initialize the external events
             -----------------------------------------------------------------*/
            function ini_events(ele) {
                ele.each(function() {

                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    }

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    })

                })
            }

            ini_events($('#external-events div.external-event'))

            /* initialize the calendar
             -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date()
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear()

            var Calendar = FullCalendar.Calendar;
            //   var Draggable = FullCalendarInteraction.Draggable;

            var containerEl = document.getElementById('external-events');
            var checkbox = document.getElementById('drop-remove');
            var calendarEl = document.getElementById('calendar');

            // initialize the external events
            // -----------------------------------------------------------------



            calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid'],
                locale: 'es',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                'themeSystem': 'bootstrap',
                //Random default events
                events: [],
                eventDataTransform: function(eventData) {
                    if (eventData.start == null) { //catches null or undef
                        eventData.start = eventData.end; //some time that won't be used
                        // eventData.end = moment("1975","yyyy");
                    }
                    return eventData;
                },
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar !!!
            });

            calendar.render();
            // $('#calendar').fullCalendar()

        })
    </script>
    <script>
        var url = "{{ url('/') }}";
        var string = "";
        $(document).ready(function() {

            if (RegExp('multipage', 'gi').test(window.location.search)) {
                introJs().setOption('doneLabel', 'Siguiente Pagina').start().oncomplete(function() {
                    window.location.href = url + '/manageassistance/massive?multipage=true';
                });

                loadPersonal("436039")
            }


        });

        $('#employe').on('keypress', function(e) {
            patron = /^([0-9])*$/;

            if (patron.test(String.fromCharCode(e.which))) {
                string += String.fromCharCode(e.which);
            }

            if (e.which == 13) {

                loadPersonal($(this).val());

                string = "";
            }
        });

        function updateCalendarData(data) {
            var events = JSON.parse(data);
            //this function reload events
            removeCalendarData();
            calendar.addEventSource(events);
            calendar.refetchEvents();
        }

        function removeCalendarData() {
            var eventSources = calendar.getEventSources();
            var len = eventSources.length;
            for (var i = 0; i < len; i++) {
                eventSources[i].remove();
            }
        }

        $('#formAssistance').submit((e) => {
            e.preventDefault();
            var datos = {};

            $("form#formAssistance :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $("#formAssistance").attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    //$('#createModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        $('#formPermission').submit((e) => {
            e.preventDefault();
            var datos = {};

            $("form#formPermission :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $("#formPermission").attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    //$('#createModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        $('#formLicence').submit((e) => {
            e.preventDefault();
            var datos = {};

            $("form#formLicence :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $("#formLicence").attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    //$('#createModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        $('#formVacation').submit((e) => {
            e.preventDefault();
            var datos = {};

            $("form#formVacation :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $("#formVacation").attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    //$('#createModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        $('#formCese').submit((e) => {
            e.preventDefault();
            var datos = {};

            $("form#formCese :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $("#formCese").attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    //$('#createModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        $('#formRemove').submit(function(e) {
            e.preventDefault();
            var datos = {};
            datos['d_since_at'] = $('#d_since_at_re').val();
            datos['d_until_at'] = $('#d_until_at_re').val();
            datos['description'] = $('#description_re').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log(response);
                    // $('#removeModal').modal('hide');
                    updateCalendarData(response.calendardata);
                    showMessageBox(response);
                    clean();
                },
                error: function(response) {
                    console.log(response);
                }
            })
        });

        function clean() {
            $('#d_since_at_r').val("{{ date('Y-m-d') }}");
            $('#d_until_at_r').val("{{ date('Y-m-d') }}");
            $('#h_until_at_r').val("17:00");
            $('#h_since_at_r').val("08:00");
            // $('#description_r').val('ASISTENCIA');
            $('#description_r').val(1);

            $('#d_since_at_re').val("{{ date('Y-m-d') }}");
            $('#d_until_at_re').val("{{ date('Y-m-d') }}");
            // $('#description_re').val('ASISTENCIA');
            $('#description_r').val(1);
        }

        function editRegister(id) {
            console.log("editando registreo :" + id);
            $.ajax({
                url: url + "/manageassistance/editRegister/" + id,
                method: 'get',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#description_edit').val(response.data.id_aux_treg);
                        $('#d_since_at_edit').val(response.data.d_since_at);
                        $('#d_until_at_edit').val(response.data.d_until_at);
                        $('#h_since_at_edit').val(response.data.h_since_at);
                        $('#h_until_at_edit').val(response.data.h_until_at);
                        $('#formupdateRegister').attr('action', url + "/manageassistance/editRegister/" + id);
                        $('#btn_delete').attr('href', "javascript:eliminar(" + id + ")");
                        $('#editModal').modal('show');
                    } else {

                    }
                    // showMessageBox(response)
                },
                error: function(response) {
                    console.log(response);
                }
            })
        }

        function cleaneditregister() {}

        $('#formupdateRegister').submit(function(e) {
            e.preventDefault();
            var datos = {};
            $("form#formupdateRegister :input").each(function(i, e) {
                // var input = $(this).val(); // This is the jquery object of the input, do what you will
                datos[$(e).attr('name')] = $(e).val();
            });


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url:  $('#formupdateRegister').attr('action'),
                method: 'POST',
                data: datos,
                success: function(response) {
                    console.log("hollaa");
                    updateCalendarData(response.calendardata);
                    // clean();
                    showMessageBox(response);
                    // $('#editModal').modal('hide');
                },
                error: function(response) {
                    console.log(response);
                }
            })

        });

        function loadPersonal(string) {
            $.ajax({
                url: url + '/manageassistance/searchEmploye/' + string,
                type: 'get',
                success: function(response) {

                    //console.log(response);
                    $('#message-employe').removeClass().text('');
                    if (response.success) {
                        $('#message-employe').append('<div>').addClass(' alert alert-success').text(response
                            .message);
                        /*-$('#formRegister').attr('action', url+'/manageassistance/register/'+response.employe.id);
                        $('#formRemove').attr('action', url+'/manageassistance/remove/'+response.employe.id);*/
                        $('#formAssistance').attr('action', url + '/manageassistance/regassistance/' + response
                            .employe.id);
                        $('#formPermission').attr('action', url + '/manageassistance/regpermission/' + response
                            .employe.id);
                        $('#formLicence').attr('action', url + '/manageassistance/reglicence/' + response
                            .employe.id);
                        $('#formVacation').attr('action', url + '/manageassistance/regvacation/' + response
                            .employe.id);
                        $('#formCese').attr('action', url + '/manageassistance/regcese/' + response.employe.id);

                        updateCalendarData(response.assistance);
                        $('#result').attr('hidden', false);
                        calendar.render();
                    } else {
                        $('#message-employe').append('<div>').addClass('alert alert-danger').text(response
                            .message);
                        // calendar.eventSource.remove()
                        $('#result').attr('hidden', true);
                        removeCalendarData();

                    }

                },
                error: function(response) {}
            });
        }

        function showMessageBox(response) {
            Swal.fire({
                // position: 'top-end',
                icon: response.icon,
                title: response.title,
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            })
        }

        function eliminar(id) {
            $.ajax({
                url: url + '/manageassistance/delete/' + id,
                type: 'get',
                success: function(response) {
                    console.log(response);
                    $('#editModal').modal('hide');
                    updateCalendarData(response.calendardata);
                }
            });

        }
    </script>
@endsection
