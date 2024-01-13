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
	
	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-gestione-richieste.html");
    $connectionOK = false;
	$infoMessage = "";
	$errorMessage = "";
	$listaRichieste="";
	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			$resultListaRichieste = $connection->getListaRichieste();
			// Verifico se sono state trovate delle richieste.
			if ($resultListaRichieste == null) {
				$infoMessage = "<p class=\"info-message\">Nessun richiesta trovata.</p>";
			} else {
				$listaRichieste = "<ul id=\"lista-richieste\">";
				// Ciclo i prodotti ottenuti per stamparli in pagina.
				foreach ($resultListaRichieste as $richieste) {
					$toRead = "";
					// Se lo stato è "Da Leggere" aggiungiamo la classe per evidenziala.
					if ($richieste["state_id"] == 1) {
						$toRead = "richiesta-da-leggere";
					}
					$date=date_create($richieste["creation_date"]);
					$listaRichieste .= 
					"<li>" .
						"<dl class=\"richiesta $toRead\">" .
							"<dt>Stato richiesta:</dt>" .
							"<dd class=\"stato\">" . $richieste["nome_stato"] . "</dd>" .
							"<dt>Data richiesta:</dt>" .
							"<dd><time datetime=\"". $richieste["creation_date"] . "\">" . date_format($date,"d F Y") . "</time></dd>" .
							"<dt>Email richiedente:</dt>" .
							"<dd>" . $richieste["email"] . "</dd>" .
							"<dt>Nome richiedente:</dt>" .
							"<dd>" . $richieste["name"] . "</dd>" .
						"</dl>" .
						"<a class=\"link-button\" href=\"area-riservata-richiesta.php?message_id=" . $richieste["message_id"] . "\" title=\"Vai al dettaglio richiesta\">></a>" .
					"</li>";
				}
				$listaRichieste .= "</ul>";
			}
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
	$paginaHtml = str_replace ("{listaRichieste}", $listaRichieste, $paginaHtml);
	echo $paginaHtml;
?>