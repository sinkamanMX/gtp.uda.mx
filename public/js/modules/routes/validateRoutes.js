var map  = null;
var infoWindow;
var geocoder;
var infoLocation;
var markerOrigen,markerDestino;
var markers = [];
var bounds;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var bControlDraw=1;

$().ready(function() {

$('.noEnterSubmit').keypress(function(e){
    if ( e.which == 13 ) return false;
    //or...
    if ( e.which == 13 ) e.preventDefault();
});    
    
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
            $("#divpErrorLogin").hide('slow');
            if($("#inputLatOrigen").val()!="" && $("#inputLonOrigen").val()!="" && 
                $("#inputLatDestino").val()!="" && $("#inputLonDestino").val()!=""){
                form.submit();    
            }else{
                $("#divpErrorLogin").show('slow');
            }
        }
    }); 

    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 

    initMapToDraw();
});

function initMapToDraw(){
    infoWindow = new google.maps.InfoWindow;
    directionsDisplay = new google.maps.DirectionsRenderer();
    geocoder = new google.maps.Geocoder();

    var mapOptions = {
        zoom: 5,
        center: new google.maps.LatLng(19.435113686545755,-99.13316173010253)
    };

    map = new google.maps.Map(document.getElementById('mapOrigen'),mapOptions);    

    var valInputOrigen      = (document.getElementById('inputSearch'));
    var autCompleteOrigen   = new google.maps.places.Autocomplete(valInputOrigen);

    google.maps.event.addListener(autCompleteOrigen, 'place_changed', function() {      
        var place = autCompleteOrigen.getPlace();
        if (!place.geometry) {
          return;
        }

        if(bControlDraw==1){
            $("#inputLatOrigen").val(place.geometry.location.lat());
            $("#inputLonOrigen").val(place.geometry.location.lng());
            if( $("#inputLatOrigen").val()!="" && $("#inputLatOrigen").val()!="0" &&
                $("#inputLonOrigen").val()!="" && $("#inputLonOrigen").val()!="0"
                ){
                setMarker(0);   
            }            
        }else{
            $("#inputLatDestino").val(place.geometry.location.lat());
            $("#inputLonDestino").val(place.geometry.location.lng());
            if( $("#inputLatDestino").val()!="" && $("#inputLatDestino").val()!="0" &&
                $("#inputLonDestino").val()!="" && $("#inputLonDestino").val()!="0"
                ){
                setMarker(1);   
            }
        }
    });   

      google.maps.event.addListener(map, 'click', function(event) {
        if(bControlDraw==1){
            $("#inputLatOrigen").val(event.latLng.lat());
            $("#inputLonOrigen").val(event.latLng.lng());
            if( $("#inputLatOrigen").val()!="" && $("#inputLatOrigen").val()!="0" &&
                $("#inputLonOrigen").val()!="" && $("#inputLonOrigen").val()!="0"
                ){
                setMarker(0);                   
            }            
        }else{
            $("#inputLatDestino").val(event.latLng.lat());
            $("#inputLonDestino").val(event.latLng.lng());
            if( $("#inputLatDestino").val()!="" && $("#inputLatDestino").val()!="0" &&
                $("#inputLonDestino").val()!="" && $("#inputLonDestino").val()!="0"
                ){
                setMarker(1);   
            }
        }
      });  
    bounds = new google.maps.LatLngBounds();      
    directionsDisplay.setMap(map);
    if($("#optReg").val()=='update'){
        setMarker(0);
        setMarker(1);
    }
}

function setMarker(optionMarker){
    var latMarker = 0;
    var lonMarker = 0;
    var position  = null;
    removeMap(optionMarker);
    if(optionMarker==0){            
        latMarker   = $("#inputLatOrigen").val();
        lonMarker   = $("#inputLonOrigen").val();
        position    = new google.maps.LatLng(latMarker, lonMarker);

        markerOrigen = new google.maps.Marker({
            map: map,
            position: position,
            draggable:true,
            animation: google.maps.Animation.DROP,
            title:  "Origen",
            icon:   '/images/assets/origen.png'
        }); 

        google.maps.event.addListener(markerOrigen, 'click', toggleBounce); 

        google.maps.event.addListener(markerOrigen, "dragend", function(event) {
            $("#inputLatOrigen").val(event.latLng.lat());
            $("#inputLonOrigen").val(event.latLng.lng());
            if( $("#inputLatOrigen").val()!="" && $("#inputLatDestino").val()!="0" &&
                $("#inputLonOrigen").val()!="" && $("#inputLonOrigen").val()!="0"
                ){
                setMarker(0);                        
            }
        });   
        bControlDraw=2;
        $("#btnClean").show('slow');
    }else{
        latMarker   = $("#inputLatDestino").val();
        lonMarker   = $("#inputLonDestino").val();
        position = new google.maps.LatLng(latMarker, lonMarker);

        markerDestino = new google.maps.Marker({
            map: map,
            position: position,
            draggable:true,
            animation: google.maps.Animation.DROP,
            title:  "Origen",
            icon:   '/images/assets/destino.png'
        }); 

        google.maps.event.addListener(markerDestino, 'click', toggleBounce);    

        google.maps.event.addListener(markerDestino, "dragend", function(event) {
            $("#inputLatDestino").val(event.latLng.lat());
            $("#inputLonDestino").val(event.latLng.lng());
            if( $("#inputLatDestino").val()!="" && $("#inputLatDestino").val()!="0" &&
                $("#inputLonDestino").val()!="" && $("#inputLonDestino").val()!="0"
                ){
                setMarker(1);
            }
        }); 
        bControlDraw=3;
        $("#btnClean").show('slow');
    }

    calcRoute(); 
    map.setZoom(18);
    map.panTo(position);     
}


function calcRoute() {
    if($("#inputLatOrigen").val()!="" && $("#inputLonOrigen").val()!="" && 
        $("#inputLatDestino").val()!="" && $("#inputLonDestino").val()!=""){
        var latsOrigen  = new google.maps.LatLng($("#inputLatOrigen").val(), $("#inputLonOrigen").val());
        var lastDestino = new google.maps.LatLng($("#inputLatDestino").val(), $("#inputLonDestino").val());
        var request = {
          origin: latsOrigen,
          destination: lastDestino,
          travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
          directionsDisplay.setMap(map);
        }
        });
    }
}

function cleanMap(){
    bControlDraw=1;
    position   = new google.maps.LatLng(19.435113686545755,-99.13316173010253);
    directionsDisplay.setMap(null);
    markerOrigen.setMap(null);
    markerDestino.setMap(null);
    $("#btnClean").hide('slow');
    $("#inputSearch").val("");
}

function removeMap(optionMarker){
    if(optionMarker==0 && markerOrigen!=null){      
        markerOrigen.setMap(null);
    }else if(optionMarker==1 && markerDestino!=null){       
        markerDestino.setMap(null);
    }
}

function toggleBounce() {
  if (marker.getAnimation() != null) {
    marker.setAnimation(null);
  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
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
