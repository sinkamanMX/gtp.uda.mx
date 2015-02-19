var mapOrigen  = null;
var mapDestino = null;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;

$().ready(function() {
    
    $("#FormData").validate({
        rules: {
            inputDescripcion    : "required",
            inputDirOrigen      : "required",
            inputDirDestino     : "required",
            inputTiempo         : "required",
            inputStatus         : "required"
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputDescripcion    : "Campo Requerido",
            inputDirOrigen      : "Campo Requerido",
            inputDirDestino     : "Campo Requerido",
            inputTiempo         : "Campo Requerido",
            inputStatus         : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    }); 

    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 
});

function initMapToDraw(){
    /*
    infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    mapOrigen = new google.maps.Map(document.getElementById('mapOrigen'),mapOptions);    
    mapDestino= new google.maps.Map(document.getElementById('mapDestino'),mapOptions);  

*/
}

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}

function deleteRow(){	
	var idItem = $("#inputDelete").val();
    $.ajax({
        url: "/admin/routes/getinfo",
        type: "GET",
        dataType : 'json',
        data: { catId : idItem, 
        		optReg: 'delete'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'deleted'){
            	$("#modalConfirmDelete").modal('hide'); 
            }else if(result == 'problem'){
                alert("hubo problema");          
            }else{
                alert("no hay data");          
            }
        }
    });    
}
