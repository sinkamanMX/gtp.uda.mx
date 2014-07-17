function getoptionsCbo(idCboTo,classObject,idObject){	
	$("#div"+idCboTo).html("Cargando Información");
    $.ajax({
        url: "/main/dashboard/getselect",
        type: "GET",
        data: { catId : idObject, 
        		oprDb : classObject },
        success: function(data) { 
        	$("#div"+idCboTo).html("");
        	var dataCbo = '<select class="span4 m-wrap chosen-select" id="input'+idCboTo+'" name="input'+idCboTo+'">';
        	if(data!="no-info"){
        		dataCbo += '<option value="">Seleccionar una opción</option>'+data+'</select>';
        	}else{
				dataCbo += '<option value="">Sin Información</option>';
        	}
        	dataCbo += '</select>';
									
        	$("#div"+idCboTo).html(dataCbo);
			$(".chosen-select").chosen({disable_search_threshold: 10});
        }
    });  	
}

