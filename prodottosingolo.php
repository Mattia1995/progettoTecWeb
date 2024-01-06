<?php
	require_once "./php/DBAccess.php";
	use DB\DBAccess;
	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');
	$paginaHtml = file_get_contents ("./prodottosingolo.html");
    $connectionOK = false;
	$errorMessage = "";
    $errorClassNotShowElement = "";
	$product_id = null;
	$nomeArticolo = "";
    $descrizioneArticolo = "";
    $prezzoArticolo = "";
    $prezzoScontatoArticolo = "";
    $marchioArticolo = "";
    $materialeArticolo = "";
    $coloreArticolo = "";
    $immagineArticolo = "";
	$categoria = "";
	
    if (isset ($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
    }
	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			if ($product_id != null) {
                $listaArticoli = $connection->getProduct ($product_id);
                if ($listaArticoli != null && sizeof($listaArticoli) > 0) {
                    $articolo = $listaArticoli[0];
                    $nomeArticolo = $articolo["name"];
                    $descrizioneArticolo = $articolo["description"];
                    $prezzoArticolo = $articolo["price"] . " €";
                    $marchioArticolo = $articolo["brand"];
                    $coloreArticolo = $articolo["color"];
                    $materialeArticolo = $articolo["material"];
                    $categoria = $articolo["nome_cat"];
					$urlArticolo = substr($articolo["image_url"], 1);
                    $immagineArticolo = "<div id=\"immagineProdotto\" class=\"sezioneProdottoSingolo{classNotShowElement}\">
						<img src=\"" . $urlArticolo . "\" alt=\"\">
					</div>";
                    // Gestione campi facoltativi.
                    if ($articolo["discounted_price"] != null){
                        $prezzoScontatoArticolo = $articolo["discounted_price"];
                    }
                } else {
                    // Se viene fornito un product_id non esistente allora faccio redirect alla pagina del nuovo prodotto.
                    header("Location:./vetrina.php");
                    // Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
                    if ($connectionOK) {
                        $connection->closeConnection();
                    }
                    exit;
                }
            } else {
				// Se non viene fornito un product_id allora faccio redirect alla pagina del nuovo prodotto.
				header("Location:./vetrina.php");
				// Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
				if ($connectionOK) {
					$connection->closeConnection();
				}
				exit;
			}
		} else {
			$nomeArticolo = "Errore, articolo non trovato";
			$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
			$errorClassNotShowElement = " class-not-show-element";
		}
	} catch (Exception $e) {
		$nomeArticolo = "Errore, articolo non trovato";
		$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
		$errorClassNotShowElement = " class-not-show-element";
	} finally {
		// Se sono riuscito ad aprire con successo la connessione ed è stata emessa un'eccezione per altri motivi, allora chiudo la connessione.
		if ($connectionOK) {
			$connection->closeConnection();
		}
	}

	$paginaHtml = str_replace ("{errorMessage}", $errorMessage, $paginaHtml);
	$paginaHtml = str_replace ("{nomeArticolo}", $nomeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{descrizioneArticolo}", $descrizioneArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{immagineArticolo}", $immagineArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{categoria}", $categoria, $paginaHtml);
	$paginaHtml = str_replace ("{marchioArticolo}", $marchioArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{prezzoArticolo}", $prezzoArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{materialeArticolo}", $materialeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{coloreArticolo}", $coloreArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{classNotShowElement}", $errorClassNotShowElement, $paginaHtml);
	echo $paginaHtml;
?>