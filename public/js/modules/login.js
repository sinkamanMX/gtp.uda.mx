$( document ).ready(function() {
    $("input").keypress(function(event) {
        if (event.which == 13) {
            validateLogin()
        }
    });  

});

function validateLogin(){
    $("#divpErrorLogin").removeClass('alert-error').hide('slow');
    var uname = $("#inputUser").val();
    var upass = $("#inputPassword").val();
    if(uname==""){        
        $("#pErrorLogin").html("Favor de ingresar el correo electronico.");
        $("#divpErrorLogin").addClass('alert-error').show('slow');
        return false;
    }
    
    if(upass==""){
        $("#pErrorLogin").html("Favor de ingresar la contraseña.");
        $("#divpErrorLogin").addClass('alert-error').show('slow');
        return false;   
    }else{
        logIn(uname,upass);
    }
}

function logIn(user,pass){
    $("#divpErrorLogin").hide('slow');

    $.ajax({
        url: "/admin/main/login",
        type: "GET",
        dataType : 'json',
        data: { usuario: user, contrasena: pass , md : 'lg'},
        success: function(data) {
            var result = data.answer; 

            if(result == 'logged'){
                location.href='/main/dashboard/index';
            }else if(result == 'problem'){
                $("#pErrorLogin").html("Por cuestion de seguridad solo se puede ingresar una vez por usuario.");
                $("#divpErrorLogin").addClass('alert-error').show('slow');
            }else{
                $("#divpErrorLogin").addClass('alert-error').show('slow');
                $("#pErrorLogin").hide('slow').html("Usuario y/o contraseña incorrectos");

            }
        }
    });
}