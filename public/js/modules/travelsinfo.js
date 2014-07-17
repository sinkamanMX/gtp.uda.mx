
$( document ).ready(function() {
  /*$(".chosen-select").chosen({disable_search_threshold: 10});*/
  $('#tabs').tab();
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var dateInter  = parseInt(nowTemp.getMonth())+1;  
        var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
        var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

        if($("#inputFechaIn").val()=="0000-00-00" || $("#inputFechaIn").val()==""){
          $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
        }

        var checkin = $('#inputFechaIn').datepicker({
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function(ev) {
          if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
            newDate.setDate(newDate.getDate() + 1);
            checkout.setValue(newDate);
          }
          checkin.hide();
          $('#inputFechaFin').prop('disabled', false);
          $('#inputFechaFin')[0].focus();
        }).data('datepicker');
        var checkout = $('#inputFechaFin').datepicker({
          onRender: function(date) {
            return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function(ev) {
          checkout.hide();
        }).data('datepicker');

  $("#FormData").validate({
        rules: {
          inputFechaIn: {
            required: true,
            date: true
          },  
          inputFechaFin: {
            required: true,
            date: true
          },  
          inputNoTravel:    "required",
          inputDescripcion: "required",
          inputSucursal:    "required",
          inputCliente:     "required",
          inputTransportista: "required",
          inputUnidades:    "required",
          inputOperadores:  "required"
        },
        messages: {
            inputNoTravel:    "Campo Requerido",
            inputSucursal:    "Campo Requerido",
            inputDescripcion: "Campo Requerido",
            inputCliente:     "Debe de seleccionar una opción",
            inputTransportista: "Debe de seleccionar una opción",
            inputUnidades:    "Debe de seleccionar una opción",
            inputOperadores:  "Debe de seleccionar una opción",  
            inputFechaIn: {
               required: "Campo Requerido",
               date: "Ingresar una fecha válida"
            },
            inputFechaFin: {
               required: "Campo Requerido",
               date: "Ingresar una fecha válida"
            }
        },
        
        submitHandler: function(form) {
            var error= 0;

            if($("#inputSucursal").val()=="" || $("#inputSucursal").val()=="-1"){
              alert("Debe de seleccionar una Sucursal");
              error++;
              return false;
            }

            if($("#inputCliente").val()==""  || $("#inputCliente").val()=="-1"){
             alert("Debe de seleccionar un Cliente"); 
             error++;
             return false;
            }

            if($("#inputTransportista").val()=="" || $("#inputTransportista").val()=="-1"){
              alert("Debe de seleccionar un Transportista");
              error++;
              return false;
            }

            if($("#inputUnidades").val()=="" || $("#inputUnidades").val()=="-1"){
              alert("Debe de seleccionar una Unidad");
              error++;
              return false;
            }        
            
            if($("#inputOperadores").val()=="" || $("#inputOperadores").val()=="-1"){
              alert("Debe de seleccionar un Operador");
              error++;
              return false;
            } 
            
            form.submit();  
            
        }
    });    

      $('#dataTable').dataTable( {
    "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
    "sPaginationType": "bootstrap",
    "bDestroy": true,
    "bLengthChange": false,
    "bPaginate": true,
    "bFilter": true,
    "bSort": true,
    "bJQueryUI": true,
    "iDisplayLength": 10,      
    "bProcessing": true,
    "bAutoWidth": true,
    "bSortClasses": false,
        "oLanguage": {
            "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
            "sEmptyTable": "Sin registros.",
            "sInfoEmpty" : "Sin registros.",
            "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
            "sLoadingRecords": "Leyendo información",
            "sProcessing": "Procesando",
            "sSearch": "Buscar:",
            "sZeroRecords": "Sin registros",
            "oPaginate": {
              "sPrevious": "Anterior",
              "sNext": "Siguiente"
            }          
        }
  } );
});