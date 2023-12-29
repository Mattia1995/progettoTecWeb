<?php
	require_once "../php/DBAccess.php";
	use DB\DBAccess;

	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');
	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-richiesta.html");
    $message_id = null;
	$pageTitle = "Richiesta di: ";
	$messageDescription = "";
	$messageDate = "";
	$evadiAction = "";

    if (isset ($_GET['message_id'])) {
        $message_id = $_GET['message_id'];
    } else {
		// Se non viene fornito un message_id allora faccio redirect alla pagina della lista richieste.
		header("Location:./area-riservata-gestione-richieste.php");
		exit;
	}
	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			$resultRichiesta = $connection->getRichiesta($message_id);
			// Se l'id non corrisponde a una richiesta esistente faccio redirect alla lista delle richieste.
			if ($resultRichiesta == null || sizeof($resultRichiesta) == 0) {
				header("Location:./area-riservata-gestione-richieste.php");
				// Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
				if ($connectionOK) {
					$connection->closeConnection();
				}
				exit;
			} else {
				$richiesta = $resultRichiesta[0];
				// Se la richiesta è da evadere e prima era in stato "Da Leggere" allora la evado.
				if (isset ($_GET['evadi']) && $_GET['evadi']) {
					if ($richiesta["state_id"] == 1) {
						$resultUpdate = $connection->evadiRichiesta($message_id);
						if (!$resultUpdate) {
							$errorMessage = "<p class=\"error-message\">Erorre nell'aggiornamento della richiesta riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
						}
					}
					header("Location:./area-riservata-richiesta.php?message_id=$message_id");
					// Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
					if ($connectionOK) {
						$connection->closeConnection();
					}
					exit;
				}
				$pageTitle = $pageTitle . $richiesta["name"];
				$messageDescription = $richiesta["message"];
				$date = date_create($richiesta["creation_date"]);
				$messageDate = date_format($date,"d F Y");
				// Se lo stato è "Da Leggere" aggiungiamo il pulsante per evaderla.
				if ($richiesta["state_id"] == 1) {
					$evadiAction = "<a class=\"link-button\" href=\"?message_id=$message_id&evadi=true\">CONTRASSEGNA COME EVASA</a>";
				}
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
	$paginaHtml = str_replace ("{pageTitle}", $pageTitle, $paginaHtml);
	$paginaHtml = str_replace ("{messageDescription}", $messageDescription, $paginaHtml);
	$paginaHtml = str_replace ("{messageDate}", $messageDate, $paginaHtml);
	$paginaHtml = str_replace ("{evadiAction}", $evadiAction, $paginaHtml);
	echo $paginaHtml;
?>