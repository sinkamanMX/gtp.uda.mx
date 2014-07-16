function getoptionsCbo(idCboTo,classObject,idObject){
	$("#"+idCboTo).find('option').remove();

    $.ajax({
        url: "/main/dashboard/getselect",
        type: "GET",
        dataType : 'json',
        data: { catId : idObject, 
        		oprDb : classObject },
        success: function(data) { 
			$("#"+idCboTo).append(data);
        }
    });  	
}

