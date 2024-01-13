<?php
	require_once "../php/DBAccess.php";
	use DB\DBAccess;

	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');
	
    // Verifico se è stato fatto correttamente il login, in caso contrario rimando alla pagina di login.
    session_start();
    if(!$_SESSION['islogged']){
        header('Location: area-riservata-login.php');
        exit;
    }

	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-gestione-articoli.html");
    $connectionOK = false;
	$infoMessage = "";
	$errorMessage = "";
	$listaArticoli="";
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
					$prezzoScontatoStringa = "";
					if ($articolo["discounted_price"] != null) { 
						$prezzoScontato = str_replace (".", ",", $articolo["discounted_price"]);
						$prezzoScontatoStringa = "<dt>Prezzo scontato:</dt>" . "<dd>" . $prezzoScontato . " €</dd>";
					}
					$listaArticoli .= 
					"<li>" .
						"<img src=\"" . $articolo["image_url"] . "\" alt=\"\">" .
						"<dl>" .
							"<dt>Nome prodotto:</dt>" .
							"<dd>" . $articolo["name"] . "</dd>" .
							"<dt>Categoria:</dt>" .
							"<dd>" . $articolo["nome_cat"] . "</dd>" .
							"<dt>Prezzo:</dt>" .
							"<dd>" . $prezzo . " €</dd>" .
							$prezzoScontatoStringa .
						"</dl>" .
						"<a class=\"link-button\" href=\"area-riservata-articolo.php?product_id=" . $articolo["product_id"] . "\" title=\"Modifica " .$articolo["name"]  . "\">Modifica</a>" .
					"</li>";
				}
				$listaArticoli .= "</ul>";
			}
			
			// Gestione lista di categorie.
			$categoryLinkList = "<div class=\"category-container\">";
			$categoryLinkList .= "<p>Filtro per categorie:</p>";
			if ($category_id == null) {
				$categoryLinkList .= "<span>Tutte</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"area-riservata-gestione-articoli.php\">Tutte</a>";
			}
			if ($category_id == 1) {
				$categoryLinkList .= "<span>Chitarra</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=1\">Chitarra</a>";
			}
			if ($category_id == 2) {
				$categoryLinkList .= "<span>Pianoforte</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=2\">Pianoforte</a>";
			}
			if ($category_id == 3) {
				$categoryLinkList .= "<span>Batteria</span>";
			} else {
				$categoryLinkList .= "<a class=\"link-button\" href=\"?category_id=3\">Batteria</a>";
			}
			$categoryLinkList .= "</div>";
		} else {
			$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattare l'amministratore del sito.</p>";
		}
	} catch (Exception $e) {
		$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattare l'amministratore del sito.</p>";
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