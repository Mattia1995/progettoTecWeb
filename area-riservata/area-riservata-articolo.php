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

	$paginaHtml = file_get_contents ("../area-riservata/area-riservata-articolo.html");
    $connectionOK = false;
    $pageTitle = "Nuovo articolo";
    $testoBottoneForm = "Inserisci";
    $fullWidthButton = "";
    $bottoneElimina = "";
    $toDel = false;
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
    $immagineArticoloLabel = "Immagine articolo:";
    $materialeArticolo = "";
    $messaggiPerForm = "";
    $idCategoria = null;
    $articolo = null;
    $product_id = null;
    $successFormMessage = "";

    if (isset ($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
    } else {
        $fullWidthButton = "form-full-width";
    }
    if (isset ($_GET['to_del'])) {
        $toDel = $_GET['to_del'];
    }
	try {
		$connection = new DBAccess();
		$connectionOK = $connection -> openDbConnection ();
        $next_product_id = 1;
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
                    $immagineArticoloLabel = "Immagine articolo presente, per sostituirla cliccare qui.";
                    $testoBottoneForm = "Aggiorna";
                    $bottoneElimina = "
                    <div class=\"form-col-50\">
                        <a class=\"form-button\" id=\"delete-button\" href=\"?product_id=$product_id&to_del=true\" onclick=\"return confirmDialog()\">Elimina</a>
                    </div>";
                } else {
                    // Se viene fornito un product_id non esistente allora faccio redirect alla pagina del nuovo prodotto.
                    header("Location:./area-riservata-articolo.php");
                    // Chiudo la connessione, se aperta correttamente, visto che non passa nel finally con exit.
                    if ($connectionOK) {
                        $connection->closeConnection();
                    }
                    exit;
                }
            } else {
                // Essendo in inserimento ottengo l'ultimo id inserito per avere il prossimo utile per salvare l'immagine.
                $max_product_id = $connection->getMaxProductId ();
                if ($max_product_id != null && sizeof($max_product_id) > 0) {
                    $next_product_id = $max_product_id[0]["max"] + 1;
                }
            }
            // Se il prodotto esiste ed è stato passato toDel a true allora cancello l'articolo.
            if ($toDel && $product_id != null && $articolo != null) {
                // Cancello il prodotto.
                $resultDelete = $connection->deleteProduct($product_id);
                if (!$resultDelete) {
                    $messaggiPerForm = "<p class=\"error-message\">Errore nell'eliminazione del prodotto riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                } else {
                    $successFormMessage = "<p class=\"success-message\">Prodotto eliminato con successo</p>";
                    $errorClassNotShowElement = "class=\"class-not-show-element\"";
                }
                // Cancello l'immagine associata al prodotto.
                unlink($immagineArticolo);
            } else {
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
                    $nomeArticoloPost = pulisciInput($_POST["nomeArticolo"]);
                    $descrizioneArticoloPost = pulisciInput($_POST["descrizioneArticolo"]);
                    $prezzoArticoloPost = pulisciInput($_POST["prezzo"]);
                    $prezzoScontatoArticoloPost = pulisciInput($_POST["prezzo_scontato"]);
                    $marchioArticoloPost = pulisciInput($_POST["marchioArticolo"]);
                    $coloreArticoloPost = pulisciInput($_POST["coloreArticolo"]);
                    $materialeArticoloPost = pulisciInput($_POST["materialeArticolo"]);
                    $idCategoriaPost = pulisciInput($_POST["categoriaArticolo"]);
                    // Verifico se è stato modificato almeno un campo, altrimenti non aggiorno.
                    if ($nomeArticolo == $nomeArticoloPost && 
                        $descrizioneArticolo == $descrizioneArticoloPost && 
                        $prezzoArticolo == $prezzoArticoloPost &&
                        $prezzoScontatoArticolo == $prezzoScontatoArticoloPost &&
                        $marchioArticolo == $marchioArticoloPost &&
                        $coloreArticolo == $coloreArticoloPost &&
                        $materialeArticolo == $materialeArticoloPost &&
                        $idCategoria == $idCategoriaPost &&
                        $_FILES["immagineArticolo"]["name"] == null) {
                            $messaggiPerForm = $messaggiPerForm . "<li>Nessun campo modificato.</li>";
                            $messaggiPerForm = "<ul class=\"error-form-message\">" . $messaggiPerForm . "</ul>";
                    } else {
                        $messaggiPerForm = insertOrUpdateProduct ($connection, $product_id, $next_product_id, $immagineArticolo);
                        // In questo caso non ci sono stati errori di validazione e quindi stampo il messaggio di inserimento avvenuto con successo.
                        if ($messaggiPerForm == null || $messaggiPerForm == '') {
                            $successFormMessage = "<p class=\"success-message\">Prodotto aggiornato con successo</p>";
                            $errorClassNotShowElement = "class=\"class-not-show-element\"";
                            if ($product_id == null) {
                                $successFormMessage = "<p class=\"success-message\">Prodotto inserito con successo</p>";
                            }
                        } else {
                            // In caso di errori di validazione aggiungo la lista degli errori e memorizzo i valori inseriti nel form.
                            $nomeArticolo = $nomeArticoloPost;
                            $descrizioneArticolo = $descrizioneArticoloPost;
                            $prezzoArticolo = $prezzoArticoloPost;
                            $prezzoScontatoArticolo = $prezzoScontatoArticoloPost;
                            $marchioArticolo = $marchioArticoloPost;
                            $coloreArticolo = $coloreArticoloPost;
                            $materialeArticolo = $materialeArticoloPost;
                            $idCategoria = $idCategoriaPost;
                        }
                    }
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

    function insertOrUpdateProduct ($connection, $product_id, $next_product_id, $immagineArticolo) {
        // Prendo tutti i campi dalla post.
        $nome = pulisciInput($_POST["nomeArticolo"]);
        $descrizione = pulisciInput($_POST["descrizioneArticolo"]);
        $prezzo = pulisciInput($_POST["prezzo"]);
        $prezzoScontato = pulisciInput($_POST["prezzo_scontato"]);
        $marchio = pulisciInput($_POST["marchioArticolo"]);
        $colore = pulisciInput($_POST["coloreArticolo"]);
        $materiale = pulisciInput($_POST["materialeArticolo"]);
        $idCategoriaSelezionata = pulisciInput($_POST["categoriaArticolo"]);
        $messaggiPerForm = "";
    
        // Validazioni.
        if ($nome == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il nome dell'articolo deve essere valorizzato.</li>";
        }
        if ($descrizione == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>La descrizione dell'articolo deve essere valorizzata.</li>";
        }
        if ($prezzo == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo dell'articolo deve essere valorizzato.</li>";
        } else {
            if ($prezzo <= 0) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo dell'articolo deve essere maggiore di zero.</li>";
            }
        }
        if ($prezzoScontato != null) {
            if ($prezzoScontato <= 0) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo scontato dell'articolo deve essere maggiore di zero.</li>";
            }
            if ($prezzoScontato >= $prezzo) {
                $messaggiPerForm = $messaggiPerForm . "<li>Il prezzo scontato dell'articolo deve essere minore del prezzo non scontato.</li>";
            }
        }
        if ($idCategoriaSelezionata == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>La categoria dell'articolo deve essere valorizzata.</li>";
        }
        if ($marchio == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il marchio dell'articolo deve essere valorizzato.</li>";
        }
        if ($colore == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il colore dell'articolo deve essere valorizzato.</li>";
        }
        if ($materiale == null) {
            $messaggiPerForm = $messaggiPerForm . "<li>Il materiale dell'articolo deve essere valorizzato.</li>";
        }
        // Verifiche immagine.
        $target_dir = "../images/upload_file_form/";
        // Creo il path dell'immagine concatenando l'id del prodotto e se nuovo il prossimo id del prodotto.
        $target_file = $target_dir . $product_id . "_" . basename($_FILES["immagineArticolo"]["name"]);
        if ($product_id == null) {
            $target_file = $target_dir . $next_product_id . "_" . basename($_FILES["immagineArticolo"]["name"]);
        }
        $checkIfFileExist = true;
        $removeOldImage = false;
        if ($_FILES["immagineArticolo"]["name"] == null) {
            // Non essendoci il file verifico se era già stato caricato in precedenza, in tal caso salvo il path vecchio.
            if ($immagineArticolo == null) {
                $messaggiPerForm = $messaggiPerForm . "<li>L'immagine è richiesta.</li>";
            } else {
                $checkIfFileExist = false;
                $target_file = $immagineArticolo;
            }
        } else {
            if (strlen(basename($_FILES["immagineArticolo"]["name"])) > 150) {
                $messaggiPerForm = $messaggiPerForm . "<li>Nome del file troppo lungo (Max 150 caratteri).</li>";
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
            if ($checkIfFileExist && !file_exists($target_file)) {
                if (!move_uploaded_file($_FILES["immagineArticolo"]["tmp_name"], $target_file)) {
                    $messaggiPerForm = "<p class=\"error-message\">Impossibile caricare il file, se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                } else {
                    $removeOldImage = true;
                }
            }
            // Se non c'è stato un errore nel caricamento del file allora provo a inserire l'articolo.
            if ($messaggiPerForm == '') {
                // Se il $product_id è null o non è stato trovato l'articolo con id fornito, allora siamo in insert.
                if ($product_id == null) {
                    $resultInsert = $connection->insertNewProduct ($nome, $descrizione, $prezzo, $marchio, $colore, $materiale, $idCategoriaSelezionata, $prezzoScontato, $target_file);
                    if (!$resultInsert) {
                        $messaggiPerForm = "<p class=\"error-message\">Errore nell'inserimento del prodotto riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                    }
                } else {
                    $resultUpdate = $connection->updateProduct ($product_id, $nome, $descrizione, $prezzo, $marchio, $colore, $materiale, $idCategoriaSelezionata, $prezzoScontato, $target_file);
                    if (!$resultUpdate) {
                        $messaggiPerForm = "<p class=\"error-message\">Errore nell'aggiornamento del prodotto riprova e se l'errore persiste contattaci tramite la pagina dedicata.</p>";
                    } else if ($removeOldImage){
                        // Dopo aver aggiornato l'articolo senza errori cancello la vecchia immagine se è stata modificata.
                        unlink($immagineArticolo);
                    }
                }
            }
        } else {
            $messaggiPerForm = "<ul class=\"error-form-message\">" . $messaggiPerForm . "</ul>";
        }
        return $messaggiPerForm;
    }
    /** ----------------------- FUNCTIONS ----------------------- */

    /** ----------------------- PRINT ----------------------- */
	$paginaHtml = str_replace ("{errorMessage}", $errorMessage, $paginaHtml);
	$paginaHtml = str_replace ("{classNotShowElement}", $errorClassNotShowElement, $paginaHtml);
	$paginaHtml = str_replace ("{testoBottoneForm}", $testoBottoneForm, $paginaHtml);
	$paginaHtml = str_replace ("{bottoneElimina}", $bottoneElimina, $paginaHtml);
	$paginaHtml = str_replace ("{fullWidthButton}", $fullWidthButton, $paginaHtml);
    $paginaHtml = str_replace ("{listacategorie}", $listaCategorie, $paginaHtml);
	$paginaHtml = str_replace ("{pageTitle}", $pageTitle, $paginaHtml);
	$paginaHtml = str_replace ("{nomeArticolo}", $nomeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{descrizioneArticolo}", $descrizioneArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{prezzoArticolo}", $prezzoArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{prezzoScontatoArticolo}", $prezzoScontatoArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{marchioArticolo}", $marchioArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{coloreArticolo}", $coloreArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{materialeArticolo}", $materialeArticolo, $paginaHtml);
	$paginaHtml = str_replace ("{successFormMessage}", $successFormMessage, $paginaHtml);
	$paginaHtml = str_replace ("{messaggiPerForm}", $messaggiPerForm, $paginaHtml);
	$paginaHtml = str_replace ("{immagineArticoloLabel}", $immagineArticoloLabel, $paginaHtml);
	echo $paginaHtml;
    /** ----------------------- PRINT ----------------------- */
?>