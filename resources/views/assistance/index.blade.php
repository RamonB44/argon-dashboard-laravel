@extends('layouts.app')

@section('title', 'AdminLTE')

@section('content_header')
    <h1 class="m-0 text-dark">Asistencias</h1>
@stop


@section('content')
<div id="message" class="d-flex justify-content-center">

</div>

<div class="container-fluid">
    <div class="row justify-content-center">
    <!-- Flexbox container for aligning the toasts -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1>Registro de <b>Asistencia
                    @if(Auth::user()->hasGroupPermission('registerIngreso'))
                        INGRESO
                    @elseif(Auth::user()->hasGroupPermission('registerSalida'))
                        SALIDA
                    @endif
                    </b></h1>
                </div>
                <div class="card-body">
                    <div class="card-group">
                        <div class="card">
                          <img src="https://image.freepik.com/vector-gratis/carnet-conducir-mano_92289-418.jpg" class="card-img-top" alt="...">
                          <div class="card-body">
                            <h5 class="card-title">1° Paso</h5>
                            <!--<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>-->
                            <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                          </div>
                        </div>
                        <div class="card">
                          <img src="https://cdn.shopify.com/s/files/1/0131/6936/0960/products/1lector-codigo-barras-pistola-inalambrico-71276-D_NQ_NP_758509-MLC27120076294_042018-F_1400x.jpg?v=1564805089" class="card-img-top" alt="...">
                          <div class="card-body">
                            <h5 class="card-title">2° Paso</h5>
                            <!--<p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>-->
                            <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                          </div>
                        </div>
                        <div class="card">
                          <img style="width: 30em;" src="https://cdn.dribbble.com/users/1516460/screenshots/5952925/messages.png" alt="...">
                          <div class="card-body">
                            <h5 class="card-title">3° Paso</h5>
                            <!--<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>-->
                            <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                          </div>
                        </div>
                        <div class="card">
                            <img style="height: 22.5em;" src="https://www.comune.sangiorgiobigarello.mn.it/images/img_articoli/starting-a-new-job.jpg" class="card-img-top" alt="...">
                            <div class="card-body">
                              <h5 class="card-title">4° Paso</h5>
                              <!--<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>-->
                              <!--<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>-->
                            </div>
                          </div>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
<audio id="audio-correct" hidden>
    <source src="{{ asset('storage/sounds/correct-sound-effect.mp3') }}" type="audio/mp3">
</audio>
<audio id="audio-incorrect" hidden>
    <source src="{{ asset('storage/sounds/incorrect-sound-effect.mp3') }}" type="audio/mp3">
</audio>
<audio id="audio-warning" hidden>
    <source src="{{ asset('storage/sounds/warning-sound-effect.mp3') }}" type="audio/mp3">
</audio>
@endsection

{{-- @section('plugins.Datatables', true) --}}
@section('js')
<script>
var manageTable = null;
var url = "{{ url('/') }}";
var string = "";

$(document).on('keypress', function (e) {

    patron = /^([0-9])*$/;
    if(patron.test(String.fromCharCode(e.which)) ){
        string += String.fromCharCode(e.which);
    }

    if (e.which == 13) {

        console.log(string);
        $.ajax({
        url: url+'/assistance/register/'+string,
        type: 'get',
        success:function(response){

            console.log(response);
            speak(response.message);
            $('#message').children().remove();
            $('#message').append(response.html);
            $('.toast').toast('show');
        },
        error:function(response){

            $("#audio-warning")[0].play();
            // alert('Ocurrio algo inesperado, comunicar a soporte: aguado.soft2016@gmail.com');
        }

        });
        string = "";
    }

});

function speak(text){
    speechSynthesis.speak(new SpeechSynthesisUtterance(text));
}
</script>
@endsection
