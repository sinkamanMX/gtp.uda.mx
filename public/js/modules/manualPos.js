$( document ).ready(function() {
  var input = (document.getElementById('inputDir'));
  var autocomplete = new google.maps.places.Autocomplete(input); 
  google.maps.event.addListener(autocomplete, 'place_changed', function() {     
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }
    });

  $("#FormData").validate({
        rules: { 
          inputLatitud: {
            required: true,
            number: true
          },
          inputLongitud: {
            required: true,
            number: true
          },
          inputAngulo: {
            required: true,
            number: true
          },
          inputVelocidad: {
            required: true,
            number: true
          },
          inputDir: "required",
          inputObservaciones  : "required",
          inputFecha: "required",            
        },
        messages: {
            inputDir       :  "Campo Requerido",
            inputObservaciones:  "Campo Requerido",
            inputLatitud    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },     
            inputLongitud    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputAngulo    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputVelocidad    : {
                     required: "Campo Requerido",
                     number: "Este campo acepta solo números"
            },
            inputFecha: "Campo Requerido",                                  
        },
        
        submitHandler: function(form) {
            form.submit();  
        }
    });    

    $('.number').keypress(function(event) {
        /*if (event.which != 46 && (event.which < 47 || event.which > 59))
        {
            event.preventDefault();
            if ((event.which == 46) && ($(this).indexOf('.') != -1)) {
                event.preventDefault();
            }
        }*/
    }); 
    $('#inputFecha').datetimepicker({
        format: "yyyy-mm-dd HH:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: true
    });    
});


$('.noEnterSubmit').keypress(function(e){
    if ( e.which == 13 ) return false;
    //or...
    if ( e.which == 13 ) e.preventDefault();
});