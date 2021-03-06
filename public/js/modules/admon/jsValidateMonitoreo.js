$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion    : "required",
            inputRazonSocial    : "required",
            inputDireccion      : "required",
            inputTelFijo        : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            inputEmail          : "required",
            inputEstatus        : "required",
            inputName           : "required",
            inputApps           : "required",
            inputUser           : {
                required    : true,
                email       : true
            },  
            inputPassword       : "required",
            inputPasswordSec    : {
                required: true,
                equalTo: "#inputPassword",
            },
            inputTelFijoUser    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },            
        },
        messages: {
            inputDescripcion	: "Campo Requerido",
            inputRazonSocial	: "Campo Requerido",
            inputDireccion	    : "Campo Requerido",
            inputTelFijo        : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },
            inputEmail          : {
                required     : "Campo Requerido",
                email        :   "Debe de ingresar un mail válido"
            },        
            inputEstatus     : "Campo Requerido",
            inputName           : "Campo Requerido",
            inputApps           : "Campo Requerido",
            inputUser          : {
                required     : "Campo Requerido",
                email        : "Debe de ingresar un mail válido"
            }, 
            inputPassword       : "Campo Requerido",
            inputPasswordSec    : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            },
            inputTelFijoUser    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            }            
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });	
    
    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 

    /*
    var bUdaUser = $("#inputClienteUDA").val();
    if(bUdaUser==0){
        $(".divUserUda").hide('fast');
        $("#inputUserUda").rules("remove", "required");
        $("#inputPasswordUda").rules("remove", "required");
    }

    if($("#optReg")=='update'){
    	$("#inputDireccion").rules("remove", "required");
		$("#inputTelFijo").rules("remove", "required");
		$("#inputTelMovil").rules("remove", "required");
		$("#nputRadio").rules("remove", "required");
		$("#inputName").rules("remove", "required");
		$("#inputApps").rules("remove", "required");
		$("#inputUser").rules("remove", "required");
		$("#inputPassword").rules("remove", "required");
		$("#inputPasswordSec").rules("remove", "required");
		$("#inputTelFijoUser").rules("remove", "required");
		$("#inputTelMovilUser").rules("remove", "required");
    }
    */
});

/*
function requiredData(optionSelect){
    if(optionSelect==1){
        $(".divUserUda").show('slow');
        $("#inputUserUda").rules("add",  {required:true});
        $("#inputPasswordUda").rules("add",  {required:true});
    }else{
        $(".divUserUda").hide('slow');
        $("#inputUserUda").rules("remove", "required");
        $("#inputPasswordUda").rules("remove", "required");
    }
}
*/

function backToMain(){
	var mainPage = $("#hRefLinkMain").val();
	location.href= mainPage;
}
