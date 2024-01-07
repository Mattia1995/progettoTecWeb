<?php
	require_once "../php/DBAccess.php";
	use DB\DBAccess;

	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');

	
    // Verifico se è stato fatto correttamente il login e in quel caso rimando all'area riservata.
    session_start();
    if($_SESSION != null && $_SESSION['islogged']){
		// Se voglio fare il logout
		if (isset ($_GET['logout']) && $_GET['logout']) {
			session_destroy();
		} else {
			header("Location: dashboard-area-riservata.php");
			exit;
		}
    }

	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-login.html");
	$connectionOK = false;
	$messaggiPerForm = "";
	try {
		$connection = new DBAccess();
		$connectionOK = $connection->openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
			if (isset($_POST['submit'])) {
				$username = $_POST['username'];
				$password = $_POST['password'];
				$hashed = hash("sha512", $password);
				$loggedUser = $connection->getUser($username, $hashed);
				if ($loggedUser == null || sizeof($loggedUser) == 0) {
					$messaggiPerForm = "<ul class=\"error-form-message\"><li>Username o password errati.</li></ul>";
				} else {
					session_start();
					$_SESSION['islogged'] = true;
					// Se l'utente si logga correttamente faccio il redirect all'area riservata.
                    header("Location: dashboard-area-riservata.php");
                    // Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
                    if ($connectionOK) {
                        $connection->closeConnection();
                    }
                    exit;
				}
			}
        }
    } catch (Exception $e) {
        $errorMessage = "<p class=\"error-message\">Si è verificato un errore durante login.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
    } finally {
        // Se sono riuscito ad aprire con successo la connessione ed è stata emessa un'eccezione per altri motivi, allora chiudo la connessione.
        if ($connectionOK) {
            $connection->closeConnection();
        }
    }
	$paginaHtml = str_replace ("{messaggiPerForm}", $messaggiPerForm, $paginaHtml);
	echo $paginaHtml;
?>