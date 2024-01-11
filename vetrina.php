<?php
	require_once "./php/DBAccess.php";
	use DB\DBAccess;

	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');

	$paginaHtml = file_get_contents ("./vetrina.html");
    $connectionOK = false;
	$infoMessage = "";
	$errorMessage = "";
	$listaArticoli="";

	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			$resultListaProdotti = $connection->getListaArticoli();
			// Verifico se sono stati trovati degli articoli.
			if ($resultListaProdotti == null) {
				$infoMessage = "<p class=\"info-message\">Nessun prodotto trovato.</p>";
			} else {
				$listaArticoli = "<ul class=\"lista-prodotti-stampa\">";
				// Ciclo i prodotti ottenuti per stamparli in pagina.
				foreach ($resultListaProdotti as $articolo) {
					$prezzoScontato = "";
					$oldPriceClass = "";
					if ($articolo["discounted_price"] != null) { 
						$prezzoScontato = "<dt class=\"dtNonAmbiguoProdotto\">Prezzo scontato:</dt>" 
						. "<dd class=\"discounted-price\">" . $articolo["discounted_price"] . " €</dd>";
						$oldPriceClass = "class=\"gray-text-line-through\"";
					}					
					// Rimuovo il primo carattere per ottenere il path corretto dell'immagine.
					$imageUrlToDb = substr($articolo["image_url"], 1);
					$listaArticoli .= 
					"<li class=\"prodottoVetrina\">" .
						"<img src=\"" . $imageUrlToDb . "\" alt=\"\">" .
						"<dl class=\"info\">" .
							"<dt class=\"dtNonAmbiguoProdotto\">Categoria:</dt>" .
							"<dd>" . $articolo["nome_cat"] . "</dd>" .
							"<dt class=\"dtNonAmbiguoProdotto\">Nome:</dt>" .
							"<dd class=\"nomeProdottoVetrina\">" . $articolo["name"] . "</dd>" .
							"<dt class=\"dtNonAmbiguoProdotto\">Prezzo:</dt>" .
							"<dd $oldPriceClass>" . $articolo["price"] . " €</dd>" .
							$prezzoScontato .
						"</dl>" .
						"<a href=\"prodottosingolo.php?product_id=" . $articolo["product_id"] . "\">Vai allo strumento</a>" .
					"</li>";
				}
				$listaArticoli .= "</ul>";
			}
		} else {
			$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
		}
	} catch (Exception $e) {
		$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
	} finally {
		// Se sono riuscito ad aprire con successo la connessione ed è stata emessa un'eccezione per altri motivi, allora chiudo la connessione.
		if ($connectionOK) {
			$connection->closeConnection();
		}
	}
	$paginaHtml = str_replace ("{errorMessage}", $errorMessage, $paginaHtml);
	$paginaHtml = str_replace ("{infoMessage}", $infoMessage, $paginaHtml);
	$paginaHtml = str_replace ("{listaArticoli}", $listaArticoli, $paginaHtml);
	echo $paginaHtml;
?>