window.addEventListener('load', initPagina);
/** Gestione dialogo conferma cancellazione */
function confirmDialog () {
	return confirm('Sei sicuro di voler eliminare il prodotto? La cancellazione sarà irreversibile.');
}

/** Gestione validazione */
var messaggiForm = {
	"nomeArticolo": ["", /^[a-zA-Z\ \'\-]{2,256}$/, "Inserisci un nome lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.", "messaggiNomeArticolo"],
	"descrizioneArticolo": ["", /^.{2,2048}$/, "Inserisci un testo lungo almeno 2 caratteri, e al massimo 2048.", "messaggiDescrizioneArticolo"],
	"materialeArticolo": ["", /^[a-zA-Z\ \'\-]{2,256}$/, "Il campo materiale dev'essere lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.", "messaggiMaterialeArticolo"],
	"marchioArticolo": ["", /^[a-zA-Z\ \'\-]{2,256}$/, "Il campo marchio dev'essere lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.", "messaggiMarchioArticolo"],
	"coloreArticolo": ["", /^[a-zA-Z\ \'\-]{2,256}$/, "Il campo colore dev'essere lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.", "messaggiColoreArticolo"],
	"prezzo": ["", /^\d+(\.\d{2}){0,1}$/, "Il prezzo dev'essere un numero intero o con due cifre dopo la virgola e maggiore del prezzo scontato.", "messaggiPrezzoArticolo"],
	"prezzo_scontato": ["", /^(\d+(\.\d{2}){0,1}){0,1}$/, "Il prezzo scontato dev'essere un numero intero o con due cifre dopo la virgola e dev'essere minore del prezzo.", "messaggiPrezzoScontatoArticolo"]
}

function initPagina() {
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
		input.onblur = function(){ verificaContenuto(this); };
	}
}

function validazioneForm() {
	var contatoreCampiErrati = 0;
	var primoCampoErrato;
	
	for (var key in messaggiForm) {
		var input = document.getElementById(key);
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
	var regex = messaggiForm[input.id][1];
	var text = input.value;
	var divErrori = document.getElementById(messaggiForm[input.id][3]);
	var prezzoScontatoErr = false;
	var prezzoErr = false;
	if (input.id == 'prezzo_scontato' && document.getElementById("prezzo").value <= +text) {
		prezzoScontatoErr = true;
		var errPrezzo = document.getElementById(messaggiForm["prezzo"][3]);
		errPrezzo.classList.remove("error-form-message");
		errPrezzo.innerHTML = "";
	}
	if (input.id == 'prezzo' && 
		(document.getElementById("prezzo_scontato").value != null && document.getElementById("prezzo_scontato").value >= +text)) {
		prezzoErr = true;
		var errPrezzo = document.getElementById(messaggiForm["prezzo_scontato"][3]);
		errPrezzo.classList.remove("error-form-message");
		errPrezzo.innerHTML = "";
	}
	if (text.search(regex) != 0 ||
		(prezzoScontatoErr) ||
		(prezzoErr)) {
		divErrori.className = "error-form-message";
		divErrori.innerHTML = messaggiForm[input.id][2];
		return false;
	} else {
		divErrori.classList.remove("error-form-message");
		divErrori.innerHTML = "";
		return true;
	}
}
