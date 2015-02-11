$().ready(function() {
	$("#FormData").validate({
        rules: {
            inputDescripcion	: "required",
            inputRazonSocial	: "required",
            inputDireccion	    : "required",
            inputRFC	    : "required",
            inputTelFijo        : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            inputApps           : "required",
            inputName           : "required",
            inputTelFijoUser    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            inputPassword       : "required",
            inputPasswordSec    : {
                required: true,
                equalTo: "#inputPassword",
            },
            inputUser           : {
                required    : true,
                email       : true
            },
        },
        messages: {
            inputDescripcion	: "Campo Requerido",
            inputRazonSocial	: "Campo Requerido",
            inputDireccion	    : "Campo Requerido",
            inputRFC	        : "Campo Requerido",
            inputTelFijo        : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },
            inputApps           : "Campo Requerido",
            inputName           : "Campo Requerido",
            inputTelFijoUser    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },
            inputPassword       : "Campo Requerido",
            inputPasswordSec    : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            },
            inputUser           : {
                required: "Campo Requerido",
                email: "Debe de ingresar un mail válido"
            },            
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