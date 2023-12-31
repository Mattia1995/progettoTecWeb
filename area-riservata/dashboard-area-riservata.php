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
	$paginaHtml = file_get_contents ("../area-riservata/dashboard-area-riservata.html");

	$reservedAreaLink = 
		"<ul id=\"contenitore-link\">
			<li id=\"contenitore-link-articoli\">
				<a href=\"area-riservata-gestione-articoli.php\">
					<div class=\"text-box\">
						<p>
							Gestisci gli articoli
						</p>
						<p>
							Accedi alla pagina dove poter modificare e inserire articoli.
						</p>
					</div>
					<div class=\"icon-box\">
						>
					</div>
				</a>
			</li>
			<li id=\"contenitore-link-richieste\">
				<a href=\"area-riservata-gestione-richieste.php\">
					<div class=\"text-box\">
						<p>
							Gestisci le richieste
						</p>
						<p>
							Accedi alla pagina dove visualizzare le richieste degli utenti.
						</p>
					</div>
					<div class=\"icon-box\">
						>
					</div>
				</a>
			</li>
		</ul>";


	$paginaHtml = str_replace ("{reservedAreaLink}", $reservedAreaLink, $paginaHtml);
	echo $paginaHtml;
?>