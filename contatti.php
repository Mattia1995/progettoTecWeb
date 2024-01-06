<?php
	require_once "./php/DBAccess.php";
	use DB\DBAccess;

	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');

	$paginaHtml = file_get_contents ("./contatti.html");
    $connectionOK = false;
	$successFormMessage = "";
	$messaggiPerForm = "";
	$nome = "";
	$email = "";
	$messaggio = "";

	try {
		// Se siamo in POST vuol dire che è stato cliccato il pulsante "submit" e quindi devo inserire una richiesta.
		if (isset($_POST['submit'])) {
			// Verifico la connessione solo se è stata inserita una richiesta.	
			$connection = new DBAccess();
			$connectionOK = $connection->openDbConnection ();
			// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
			if ($connectionOK) {		
					$messaggiPerForm = insertMessage ($connection);
					// In questo caso non ci sono stati errori di validazione e quindi stampo il messaggio di inserimento avvenuto con successo.
					if ($messaggiPerForm == null || $messaggiPerForm == '') {
						$successFormMessage = "<p class=\"success-message\">Richiesta inserita con successo</p>";
						$errorClassNotShowElement = "class=\"class-not-show-element\"";
					} else {
						// In caso di errori di validazione aggiungo la lista degli errori e memorizzo i valori inseriti nel form.
						$nome = pulisciInput($_POST["nome"]);
						$email = pulisciInput($_POST["email"]);
						$messaggio = pulisciInput($_POST["messaggio"]);
					}
			} else {
				$messaggiPerForm = "<p class=\"error-message\">Si è verificato un errore durante l'inserimento della richiesta.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite e-mail o chiamando direttamente il numero qui sopra.</p>";
			}
		}
	} catch (Exception $e) {
		$messaggiPerForm = "<p class=\"error-message\">Si è verificato un errore durante l'inserimento della richiesta.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite e-mail o chiamando direttamente il numero qui sopra.</p>";
	} finally {
		// Se sono riuscito ad aprire con successo la connessione ed è stata emessa un'eccezione per altri motivi, allora chiudo la connessione.
		if ($connectionOK) {
			$connection->closeConnection();
		}
	}

    /** ----------------------- FUNCTIONS ----------------------- */
	function pulisciInput($value) {
        // Elimina gli spazi.
        $value = trim($value);
        // Rimuove tag html (non sempre buona idea);
        $value = strip_tags($value);
        // Converte i caratteri speciali in entità html (ex &lt;)
        $value = htmlentities($value);
        return $value;
    }

	function insertMessage ($connection) {
        // Prendo tutti i campi dalla post.
		$nomePost = pulisciInput($_POST["nome"]);
		$emailPost = pulisciInput($_POST["email"]);
		$messaggioPost = pulisciInput($_POST["messaggio"]);
        $messaggiPerForm = "";
    
        // Validazioni.
        if ($nomePost == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il nome deve essere valorizzato.</li>";
        } else {
            if (!preg_match ("/^[a-zA-Z\ \'\-]{2,256}$/", $nomePost)) {
                $messaggiPerForm = $messaggiPerForm . "<li>Inserisci un nome lungo almeno 2 caratteri, e al massimo 256. Non sono ammessi numeri o simboli speciali.</li>";
            }
        }

        if ($emailPost == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>L'indirizzo e-mail deve essere valorizzato.</li>";
        } else {
            if (!preg_match("/^[a-zA-Z0-9. _-]+@[a-zA-Z0-9. -]+\.[a-zA-Z]{2,4}$/",$emailPost)) {
                $messaggiPerForm = $messaggiPerForm . "<li>L'indirizzo e-mail inserito non è valido, controlla di aver digitato correttamente la tua e-mail.</li>";
            }
        }

        if ($messaggioPost == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il messaggio deve essere valorizzato.</li>";
        } else {
            if (!preg_match("/^.{2,2048}$/",$messaggioPost)) {
                $messaggiPerForm = $messaggiPerForm . "<li>Inserisci un testo lungo almeno 2 caratteri, e al massimo 2048.</li>";
            }
        }

        // Se non ci sono errori di validazione inserisco il valore.
        if ($messaggiPerForm == '') {
			$creationDate =  date("Y-m-d");
			$resultInsert = $connection->insertNewMessage ($nomePost, $emailPost, $messaggioPost, $creationDate);
			if (!$resultInsert) {
				$messaggiPerForm = "<p class=\"error-message\">Erorre nell'inserimento della richiesta, riprova e se l'errore persiste contattaci tramite e-mail o chiamando direttamente il numero qui sopra.</p>";
			}
        } else {
            $messaggiPerForm = "<ul class=\"error-form-message\">" . $messaggiPerForm . "</ul>";
        }
        return $messaggiPerForm;
    }
    /** ----------------------- FUNCTIONS ----------------------- */
	
    /** ------------------------- PRINT ------------------------- */
	$paginaHtml = str_replace ("{successFormMessage}", $successFormMessage, $paginaHtml);
	$paginaHtml = str_replace ("{messaggiPerForm}", $messaggiPerForm, $paginaHtml);
	$paginaHtml = str_replace ("{nome}", $nome, $paginaHtml);
	$paginaHtml = str_replace ("{email}", $email, $paginaHtml);
	$paginaHtml = str_replace ("{messaggio}", $messaggio, $paginaHtml);
	$paginaHtml = str_replace ("{actionForm}", htmlspecialchars($_SERVER["PHP_SELF"]) . "#contactsForm", $paginaHtml);
	echo $paginaHtml;
?>