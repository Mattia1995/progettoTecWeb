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
	$listaArticoli = "";
	$category_id = null;
	$categoryLinkList = "";
    if (isset ($_GET['category_id'])) {
        $category_id = $_GET['category_id'];
    }
	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			$resultListaProdotti = $connection->getListaArticoli($category_id);
			// Verifico se sono stati trovati degli articoli.
			if ($resultListaProdotti == null) {
				$infoMessage = "<p class=\"info-message\">Nessun prodotto trovato.</p>";
			} else {
				$listaArticoli = "<ul class=\"lista-prodotti-stampa\">";
				// Ciclo i prodotti ottenuti per stamparli in pagina.
				foreach ($resultListaProdotti as $articolo) {
					$prezzo = str_replace (".", ",", $articolo["price"]);
					$prezzoScontatoString = "";
					$oldPriceClass = "";
					if ($articolo["discounted_price"] != null) { 
						$prezzoScontato = str_replace (".", ",", $articolo["discounted_price"]);
						$prezzoScontatoString = "<dt class=\"dtNonAmbiguoProdotto\">Prezzo scontato:</dt>" 
						. "<dd class=\"discounted-price\">" . $prezzoScontato . " €</dd>";
						$oldPriceClass = "class=\"gray-text-line-through\"";
					}					
					// Rimuovo il primo carattere per ottenere il path corretto dell'immagine.
					$imageUrlToDb = substr($articolo["image_url"], 1);
					// Protezione per non avere il title del link troppo lungo.
					$nomeArticoloPrimi30Char = substr($articolo["name"], 0, 30);
					$listaArticoli .= 
					"<li class=\"prodottoVetrina\">" .
						"<img src=\"" . $imageUrlToDb . "\" alt=\"\">" .
						"<dl class=\"info\">" .
							"<dt class=\"dtNonAmbiguoProdotto\">Categoria:</dt>" .
							"<dd>" . $articolo["nome_cat"] . "</dd>" .
							"<dt class=\"dtNonAmbiguoProdotto\">Nome:</dt>" .
							"<dd class=\"nomeProdottoVetrina\">" . $articolo["name"] . "</dd>" .
							"<dt class=\"dtNonAmbiguoProdotto\">Prezzo:</dt>" .
							"<dd $oldPriceClass>" . $prezzo . " €</dd>" .
							$prezzoScontatoString .
						"</dl>" .
						"<a href=\"prodottosingolo.php?product_id=" . $articolo["product_id"] . "\" title=\"Vai allo strumento " . $nomeArticoloPrimi30Char . "\">Vai allo strumento</a>" .
					"</li>";
				}
				$listaArticoli .= "</ul>";
			}
			
			// Gestione lista di categorie.
			$categoryLinkList = "<div class=\"category-container\">";
			$categoryLinkList .= "<p>Filtro per categorie:</p>";
			if ($category_id == null) {
				$categoryLinkList .= "<span class=\"link-button selected\">Tutte</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"vetrina.php\">Tutte</a>";
			}
			if ($category_id == 1) {
				$categoryLinkList .= "<span class=\"link-button selected\">Chitarre</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=1\">Chitarre</a>";
			}
			if ($category_id == 2) {
				$categoryLinkList .= "<span class=\"link-button selected\">Pianoforti</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=2\">Pianoforti</a>";
			}
			if ($category_id == 3) {
				$categoryLinkList .= "<span class=\"link-button selected\">Batterie</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=3\">Batterie</a>";
			}
			$categoryLinkList .= "</div>";
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
	$paginaHtml = str_replace ("{categoryLinkList}", $categoryLinkList, $paginaHtml);
	echo $paginaHtml;
?>