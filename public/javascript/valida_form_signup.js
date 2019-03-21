function validateFormSignup() {
	  var error_div = document.getElementById('div_errore');
	  
	  var username = document.forms["form_signup"]["username"].value;
	  var password = document.forms["form_signup"]["password"].value;
	  var email = document.forms["form_signup"]["email"].value;
	  var nome = document.forms["form_signup"]["nome"].value;
	  var cognome = document.forms["form_signup"]["cognome"].value;
	  var datanascita = document.forms["form_signup"]["datanascita"].value;
	  
	  var regexUsername = RegExp('[A-Za-z0-9]{4,30}');
	  var regexPassword = RegExp(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/);
	  var regexEmail = RegExp('[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$');
	  var regexNome = RegExp('[A-Za-z0-9]{1,30}');
	  var regexCognome = RegExp('[A-Za-z0-9]{1,30}');
	  var regexData = RegExp(/^([0-2][0-9]|(3)[0-1])(-)(((0)[0-9])|((1)[0-2]))(-)\d{4}$/);
	  

	  
	  if (!regexUsername.test(username.trim())) {
		  alert("Nome utente: deve contenere almeno 4 caratteri");
		  return false;
	  }
	  
	  if (!regexPassword.test(password.trim())) {
		  alert("Password : Deve contenere almeno 8 caratteri, un numero, una lettera maiuscola e una minuscola");
		 return false;
	  }

	  if (!regexEmail.test(email.trim())) {
		  alert("Email : scrivi un'email corretta es: example@gmail.com");
		 return false;
	  }
	  if (!regexNome.test(nome.trim())) {
		  alert("Nome : Scrivi il tuo nome, non usare caratteri speciali");
		 return false;
	  }
	  if (!regexCognome.test(cognome.trim())) {
		  alert("Cognome : Scrivi il tuo cognome, non usare caratteri speciali");
		 return false;
	  }
	  
	  if (!regexData.test(datanascita.trim())) {
		  alert("Data : Scrivi la data in cui sei nato, es: 10-02-1997");
		 return false;
	  }
	  
	  return true;


}
