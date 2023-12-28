window.addEventListener('load', initPagina);

var messaggiForm = {
	"nome": ["Il tuo nome", /^[a-zA-Z\ \'\-]{2,256}$/, "Inserisci un nome lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.", "messaggiNome"],
	"email": ["La tua e-mail, su cui possiamo ricontattarti", /^[a-zA-Z0-9. _-]+@[a-zA-Z0-9. -]+\.[a-zA-Z]{2,4}$/, "L'indirizzo e-mail inserito non è valido, controlla di aver digitato correttamente la tua e-mail.", "messaggiEmail"],
	"messaggio": ["La tua richiesta per noi!", /^.{2,2048}$/, "Inserisci un testo lungo almeno 2 caratteri, e al massimo 2048.", "messaggiMessaggio"]
}

function initPagina() {
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
		placeholderAccessibile(input);
		input.onfocus = function(){ rimuoviPlaceholder(this); };
		input.onblur = function(){ verificaContenuto(this); };
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
	var contatoreCampiErrati = 0;
	var primoCampoErrato;
	
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
		var placeholder = messaggiForm[input.id][0];
		var regex = messaggiForm[input.id][1];
		var text = input.value;
		
		if (!verificaContenuto(input)) {
			contatoreCampiErrati = contatoreCampiErrati + 1;
			
			if(contatoreCampiErrati == 1) {
				primoCampoErrato = input;
			}
		}
	}

	if (contatoreCampiErrati > 0) {
		primoCampoErrato.focus();
		primoCampoErrato.select();
	}

	return contatoreCampiErrati == 0;
}

function verificaContenuto(input) {
	var placeholder = messaggiForm[input.id][0];
	var regex = messaggiForm[input.id][1];
	var text = input.value;
	
	var divErrori = document.getElementById(messaggiForm[input.id][3]);
	
	if ((text == placeholder) || (text.search(regex) != 0)) {
		divErrori.className = "errorText";
		divErrori.innerHTML = messaggiForm[input.id][2];
		return false;
	}
	else {
		divErrori.classList.remove("errorText");
		divErrori.innerHTML = "";
		return true;
	}
}
