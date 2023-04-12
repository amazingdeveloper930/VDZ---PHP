function inLoggen() {

$("#loginform").submit();
	
}

$("#loginform").submit(function (e) {
e.preventDefault();

let user = $("#email").val();
let pass = $("#password").val();
let errors = "";

if(user == '') { 
$("#email").addClass("error");
errors = "error";
setTimeout(function(){ $("#email").removeClass("error"); }, 2500);
}

if(pass == '') { 
$("#password").addClass("error");
errors = "error";
setTimeout(function(){ $("#password").removeClass("error"); }, 2500);
}

if(errors == '') {
	
$(".loginformulier .button span").removeClass('fadein');
$(".loginformulier .button span").addClass('fadeout');
$(".loginformulier .button .sk-three-bounce").removeClass('fadeout');
$(".loginformulier .button .sk-three-bounce").addClass('fadein');

var data=$("#loginform").serializeArray();

 $.ajax({
	type: "POST",
	url: "php/login/authenticate.php",
	data: data,
	dataType: "html",
	success: function(result){

	//alert(result);
	
	if(result == 'Ingelogd!') {
		
	$(".quote").addClass('hidequote');
	$(".quote2").addClass('hidequote');
	$(".logo").addClass('hidequote');

	$(".homepage-menus").addClass('hidequote');

	$(".holder").addClass('hideholder');
	setTimeout(function(){ 
	//$("body").addClass('app');
	//$(".sidebar").addClass('grey');
	}, 500);	
	setTimeout(function(){ 
	window.location.href = "http://127.0.0.1:8000/leads/";
	}, 1000);
		
	} else {
	
	if(result == 'Incorrect username!') {
		
	$("#email").addClass("error");
	setTimeout(function(){ $("#email").removeClass("error"); }, 2500);	
	
	melding('Onbekend e-mailadres','rood');
				
	} else if(result == 'Incorrect password!') {
		
	$("#password").addClass("error");
	setTimeout(function(){ $("#password").removeClass("error"); }, 2500);
	
	melding('Wachtwoord onjuist','rood');
		
	} else {
		
	$("#email").addClass("error");
	setTimeout(function(){ $("#email").removeClass("error"); }, 2500);	
	
	$("#password").addClass("error");
	setTimeout(function(){ $("#password").removeClass("error"); }, 2500);	
	
	melding('Helaas, dat werkt niet.','rood');
		
	}
	
	}
					
	}
 });	

setTimeout(function(){ 
$(".loginformulier .button span").removeClass('fadeout');
$(".loginformulier .button span").addClass('fadein');
$(".loginformulier .button .sk-three-bounce").removeClass('fadein');
$(".loginformulier .button .sk-three-bounce").addClass('fadeout');
}, 2000);
	
}
	
});