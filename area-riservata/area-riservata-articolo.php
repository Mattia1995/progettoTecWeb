<?php
	require_once "../php/DBAccess.php";
	use DB\DBAccess;
	ini_set('dusplay_errors',1);
	ini_set('dusplay_startup_errors',1);
	error_reporting (E_ALL);
	setlocale (LC_ALL, 'it_IT');
	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-articolo.html");
    
    $connectionOK = false;
    $pageTitle = "Nuovo aticolo";
	$infoMessage = "";
	$errorMessage = "";
    $errorClassNotShowElement = "";
    $listaCategorie = "";
    $nomeArticolo = "";
    $descrizioneArticolo = "";
    $prezzoArticolo = "";
    $prezzoScontatoArticolo = "";
    $marchioArticolo = "";
    $coloreArticolo = "";
    $immagineArticolo = "";
    $materialeArticolo = "";
    $messaggiPerForm = "";
    $idCategoria = null;
    $articolo = null;
    $product_id = null;
    $successFormMessage = "";
    
    function pulisciInput($value) {
        // Elimina gli spazi.
        $value = trim($value);
        // Rimuove tag html (non sempre buona idea);
        $value = strip_tags($value);
        // Converte i caratteri speciali in entità html (ex &lt;)
        $value = htmlentities($value);
        return $value;
    }

    function insertOrUpdateProduct ($connection, $product_id, $immagineArticolo) {
        // Prendo tutti i campi dalla post.
        $nomeArticolo = pulisciInput($_POST["nomeArticolo"]);
        $descrizioneArticolo = pulisciInput($_POST["descrizioneArticolo"]);
        $prezzoArticolo = pulisciInput($_POST["prezzo"]);
        $prezzoScontatoArticolo = pulisciInput($_POST["prezzo_scontato"]);
        $marchioArticolo = pulisciInput($_POST["marchioArticolo"]);
        $coloreArticolo = pulisciInput($_POST["coloreArticolo"]);
        $materialeArticolo = pulisciInput($_POST["materialeArticolo"]);
        $idCategoria = pulisciInput($_POST["categoriaArticolo"]);
        $messaggiPerForm = "";

        // Validazioni.
        if ($nomeArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il nome dell'articolo deve essere valorizzato.</li>";
        }
        if ($descrizioneArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>La descrizione dell'articolo deve essere valorizzata.</li>";
        }
        if ($prezzoArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo dell'articolo deve essere valorizzato.</li>";
        } else {
            if ($prezzoArticolo <= 0) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo dell'articolo deve essere maggiore di zero.</li>";
            }
        }
        if ($prezzoScontatoArticolo != null) {
            if ($prezzoScontatoArticolo <= 0) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo scontato dell'articolo deve essere maggiore di zero.</li>";
            }
            if ($prezzoScontatoArticolo >= $prezzoArticolo) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo scontato dell'articolo deve essere maggiore del prezzo non scontato.</li>";
            }
        }
        if ($idCategoria == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>La categoria dell'articolo deve essere valorizzata.</li>";
        }
        if ($marchioArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il marchio dell'articolo deve essere valorizzato.</li>";
        }
        if ($coloreArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il colore dell'articolo deve essere valorizzato.</li>";
        }
        if ($materialeArticolo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il materiale dell'articolo deve essere valorizzato.</li>";
        }
        // Verifiche immagine
        $target_dir = "../images/upload_file_form/";
        $target_file = $target_dir . basename($_FILES["immagineArticolo"]["name"]);
        if ($_FILES["immagineArticolo"]["name"] == null) {
            if ($immagineArticolo == null) {
                $messaggiPerForm = $messaggiPerForm . "<li>L'immagine è richiesta.</li>";
            }
        } else {
            if (strlen(basename($_FILES["immagineArticolo"]["name"])) > 256) {
                $messaggiPerForm = $messaggiPerForm . "<li>Nome del file troppo lungo (Max 240 caratteri).</li>";
            } else {
                $check = getimagesize($_FILES["immagineArticolo"]["tmp_name"]);
                // Verifico se è davvero un'immagine.
                if ($check == false) {
                    $messaggiPerForm = $messaggiPerForm . "<li>Il file caricato non è valido, caricare un file con estensione .jpg o .png.</li>";
                }
            }
        }
        // Se non ci sono errori di validazione inserisco il valore.
        if ($messaggiPerForm == '') {
            // Verifico se il file esiste già e in caso contrario provo a aggiungerlo.
            if (!file_exists($target_file)) {
                if (!move_uploaded_file($_FILES["immagineArticolo"]["tmp_name"], $target_file)) {
                    $messaggiPerForm = "<p class=\"error-message\">Impossibile caricare il file, se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                }
            }
            // Se non c'è stato un errore nel caricamento del file allora provo a inserire l'articolo.
            if ($messaggiPerForm == '') {
                // Se il $product_id è null o non è stato trovato l'articolo con id fornito, allora siamo in insert.
                if ($product_id == null) {
                    $resultInsert = $connection->insertNewProduct ($nomeArticolo, $descrizioneArticolo, $prezzoArticolo, $marchioArticolo, $coloreArticolo, $materialeArticolo, $idCategoria, $prezzoScontatoArticolo, $target_file);
                    if (!$resultInsert) {
                        $messaggiPerForm = "<p class=\"error-message\">Erorre nell'inserimento del prodotto riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                    }
                } else {
                    $resultUpdate = $connection->updateProduct ($product_id, $nomeArticolo, $descrizioneArticolo, $prezzoArticolo, $marchioArticolo, $coloreArticolo, $materialeArticolo, $idCategoria, $prezzoScontatoArticolo, $target_file);
                    if (!$resultUpdate) {
                        $messaggiPerForm = "<p class=\"error-message\">Erorre nell'aggiornamento del prodotto riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                    }
                }
            }
        } else {
            $messaggiPerForm = "<ul class=\"error-form-message\">" . $messaggiPerForm . "</ul>";
        }
        return $messaggiPerForm;
    }

    //TODO: Capire se fare redirect con ID sbagliato.
    if (isset ($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
    }
	try {
		$connection = new DBAccess();
		$connectionOK = $connection -> openDbConnection ();
		// QUI VERIFICHIAMO SEMPRE LA CONNESSIONE
		if ($connectionOK) {
            // Ottengo l'articolo presente in GET.
            if ($product_id != null) {
                $listaArticoli = $connection->getProduct ($product_id);
                if ($listaArticoli != null && sizeof($listaArticoli) > 0) {
                    $articolo = $listaArticoli[0];
                    $pageTitle = $articolo["name"];
                    $nomeArticolo = $articolo["name"];
                    $descrizioneArticolo = $articolo["description"];
                    $prezzoArticolo = $articolo["price"];
                    $marchioArticolo = $articolo["brand"];
                    $coloreArticolo = $articolo["color"];
                    $materialeArticolo = $articolo["material"];
                    $idCategoria = $articolo["category_id"];
                    $immagineArticolo = $articolo["image_url"];
                    // Gestione campi facoltativi.
                    if ($articolo["discounted_price"] != null){
                        $prezzoScontatoArticolo = $articolo["discounted_price"];
                    }
                } else {
                    // Se viene fornito un product_id non esistente allora faccio redirect alla pagina del nuovo prodotto.
                    header("Location:./area-riservata-articolo.php");
                    exit;
                }
            }
            // Ottengo le categorie.
            $categorie = $connection->getCategories ();
            // QUI POPOLIAMO LA LISTA degli album verificando quello selezionato.
            foreach ($categorie as $categoria) {
                if (isset($_POST['submit'])) {
                    if (isset($_POST['categoriaArticolo']) && $categoria["category_id"] == $_POST["categoriaArticolo"]) {
                        $listaCategorie .= "<option value=\"" . $categoria["category_id"] . "\" selected>" . $categoria["name"] . "</option>";
                    } else {
                        $listaCategorie .= "<option value=\"" . $categoria["category_id"] . "\">" . $categoria["name"] . "</option>";
                    }
                } else {
                    if ($idCategoria == $categoria["category_id"]) {
                        $listaCategorie .= "<option value=\"" . $categoria["category_id"] . "\" selected>" . $categoria["name"] . "</option>";
                    } else {
                        $listaCategorie .= "<option value=\"" . $categoria["category_id"] . "\">" . $categoria["name"] . "</option>";
                    }
                }
            }
            // Se siamo in POST vuol dire che è stato cliccato il pulsante "submit" e quindi devo inserire o modificare il prodotto.
            if (isset($_POST['submit'])) {
                $messaggiPerForm = insertOrUpdateProduct ($connection, $product_id, $immagineArticolo);
                // In questo caso non ci sono stati errori di validazione e quindi stampo il messaggio di inserimento avvenuto con successo.
                if ($messaggiPerForm == null || $messaggiPerForm == '') {
                    $successFormMessage = "<p class=\"success-message\">Prodotto aggiornato con successo</p>";
                    $errorClassNotShowElement = "class=\"class-not-show-element\"";
                    if ($product_id == null) {
                        $successFormMessage = "<p class=\"success-message\">Prodotto inserito con successo</p>";
                    }
                    
                    // TODO: Capire come gestire il reinvio automatico del form con F5
                    // $nomeArticolo = "";
                    // $descrizioneArticolo = "";
                    // $prezzoArticolo = null;
                    // $prezzoScontatoArticolo = null;
                    // $marchioArticolo = "";
                    // $coloreArticolo = "";
                    // $materialeArticolo = "";
                } else {
                    // In caso di errori di validazione aggiungo la lista degli errori e memorizzo i valori inseriti nel form.
                    $nomeArticolo = pulisciInput($_POST["nomeArticolo"]);
                    $descrizioneArticolo = pulisciInput($_POST["descrizioneArticolo"]);
                    $prezzoArticolo = pulisciInput($_POST["prezzo"]);
                    $prezzoScontatoArticolo = pulisciInput($_POST["prezzo_scontato"]);
                    $marchioArticolo = pulisciInput($_POST["marchioArticolo"]);
                    $coloreArticolo = pulisciInput($_POST["coloreArticolo"]);
                    $materialeArticolo = pulisciInput($_POST["materialeArticolo"]);
                }
            }
        } else {
            $errorClassNotShowElement = "class=\"class-not-show-element\"";
			$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
		}
	} catch (Exception $e) {
        $errorClassNotShowElement = "class=\"class-not-show-element\"";
		$errorMessage = "<p class=\"error-message\">Si è verificato un errore durante il caricamento dei dati.</p><p class=\"error-message\"> Se l'errore dovesse persistere ti invitiamo a contattarci tramite i canali indicati nella pagina contatti.</p>";
	} finally {
		// Se sono riuscito ad aprire con successo la connessione ed è stata emessa un'eccezione per altri motivi, allora chiudo la connessione.
		if ($connectionOK) {
			$connection->closeConnection();
		}
	}
	$paginaHtml = str_replace ("{errorMessage}", $errorMessage, $paginaHtml);
	$paginaHtml = str_replace ("{infoMessage}", $infoMessage, $paginaHtml);
	$paginaHtml = str_replace ("{classNotShowElement}", $errorClassNotShowElement, $paginaHtml);
    $paginaHtml = str_replace ("{listacategorie}", $listaCategorie, $paginaHtml);
	$paginaHtml = str_replace ("{pageTitle}", $pageTitle, $paginaHtml);
	$paginaHtml = str_replace ("{nomeArticolo}", $nomeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{descrizioneArticolo}", $descrizioneArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{prezzoArticolo}", $prezzoArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{prezzoScontatoArticolo}", $prezzoScontatoArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{marchioArticolo}", $marchioArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{coloreArticolo}", $coloreArticolo, $paginaHtml);
	// $paginaHtml = str_replace ("{immagineArticolo}", $immagineArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{materialeArticolo}", $materialeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{successFormMessage}", $successFormMessage, $paginaHtml);
	$paginaHtml = str_replace ("{messaggiPerForm}", $messaggiPerForm, $paginaHtml);

    
	echo $paginaHtml;
?>