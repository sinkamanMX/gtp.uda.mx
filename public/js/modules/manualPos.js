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
          inputFecha: {
            required: true,
            date: true
          },            
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
            inputFecha: {
               required: "Campo Requerido",
               date: "Ingresar una fecha válida"
            },                                                    
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
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    $("#inputFecha").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
    var checkout = $('#inputFecha').datepicker({
      format: 'yyyy-mm-dd',
      onRender: function(date) {
        
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');          
});


$('.noEnterSubmit').keypress(function(e){
    if ( e.which == 13 ) return false;
    //or...
    if ( e.which == 13 ) e.preventDefault();
});