window.addEventListener('load', initPagina);

var messaggiForm = {
	"nome": ["Il tuo nome", /^[a-zA-Z\ \'\-]{2,256}$/, "Inserisci un nome lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali."],
	"email": ["La tua e-mail, su cui possiamo ricontattarti", /^[a-zA-Z0-9. _-]+@[a-zA-Z0-9. -]+\.[a-zA-Z]{2,4}$/, "L'indirizzo e-mail inserito non è valido, controlla di aver digitato correttamente la tua e-mail."],
	"messaggio": ["La tua richiesta per noi!", /^.{2,2048}$/, "Inserisci un testo lungo almeno 2 caratteri, e al massimo 2048."]
}

function initPagina() {
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
		placeholderAccessibile(input);
		input.onfocus = function(){ rimuoviPlaceholder(this); };
	}
}

function placeholderAccessibile(input) {
	input.value = messaggiForm[input.id][0];
}

function rimuoviPlaceholder(input) {
	if(input.value == messaggiForm[input.id][0]){
		input.value = "";
	}
}

function validazioneForm() {
	var ulErrori = document.getElementById("messaggiFormContattaci");
	if (ulErrori) { 
		ulErrori.parentNode.removeChild(ulErrori);
	}
	
	var listaErrori = [];
	var primoCampoErrato;
	
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
		var placeholder = messaggiForm[input.id][0];
		var regex = messaggiForm[input.id][1];
		var text = input.value;
		
		if ((text == placeholder) || (text.search(regex) != 0)) {
			listaErrori.push(messaggiForm[input.id][2]);
			
			if(listaErrori.length == 1) {
				primoCampoErrato = input;
			}
		}
	}

	if(listaErrori.length > 0) {
		mostraErrori(listaErrori);
		primoCampoErrato.focus();
		primoCampoErrato.select();
	}

	return listaErrori.length == 0;
}

function mostraErrori(listaErrori) {	
	var divErrori = document.getElementById("contenitoreMessaggi");
	var ulErrori = document.createElement("ul");

	ulErrori.id = "messaggiFormContattaci";
	ulErrori.className = "errorText";
	
	for (var key in listaErrori) {
		var liErrori = document.createElement('li');
		liErrori.innerHTML = listaErrori[key];
		
        ulErrori.appendChild(liErrori);
	}

	divErrori.appendChild(ulErrori);
}