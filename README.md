----- PROGETTO TecWeb Negozio strumenti musicali -----
Scrivere di seguito eventuali appunti tecnici sul progetto:
1) Non fare commit mai in main, creare un branch per persona e poi aprire PR verso main con approvazione.



------- DOMANDE VARIE:
1) Va bene la nav dentro l'header?
    -> Si va bene.
2) Ho dovuto aggiungere un div per contenere l'header, potrebbe andare bene?
    -> Va bene.
3) Tasto modifica va bene? Se con lo screen reader vai ai vari link non sentiresti solo "modifica modifica modifica ...."
    -> Rimosso span e messo nel title del tag a.
4) Contrasto nel placeholder.
    -> Non mettere il placeholder.
5) Label accetta l'attributo lang? (ad esempio per mettere la lingua di "Username").
    -> Si può usare lang sulle label anzi meglio usarlo.
6) Serve sempre la legend nei form?
    -> Non per forza, se si usa fieldset meglio di si ma fieldset non è obbligatorio se non si fa un raggruppamento sensato dei campi. Se non si vuole usare fieldset usare div.
7) Vanno bene i div per riga nei form?
    -> Si va bene come struttura
8) Serve la descrizione nella form?
    -> Non per forza dipende dal caso, meglio non aggiungere molti aria-* se il form è già chiaro.
9) Area riservata keyword ecc non vanno bloccati?
    -> Metterle sempre perché la prof lancia test automatici ma non pensarci troppo, non servono per i browser.
10) Va bene button nella pagina della richiesta? 
    -> Meglio link con link a una get del server che memorizza (Vedo appunti stefano ha fatto un esempio a lezione).
11) Va bene la gestione per firefox per l'hover dei bottoni nella lista degli articoli? 
    -> ok mettere in relazione.
12) h1 capire se lasciare il nome del negozio? 
    -> o 2 h1 o h1 nome negozio e h2 nome pagina.




------- COSE DA CONTROLLARE PAGINE PROGETTO:
1) Validare le pagine sia parte html che parte CSS.
2) Aggiungere sempre Keywords e description.
3) Aggiungere aiuti alla navigazione in tutte le pagine.
4) Verificare che non ci siano link rotti (toglie punti).
5) Fare dei test con gli strumenti indicati dalla prof (slide 96 accessibilità) -> Total validator da usare in lab, la prof usa questo per validare i progetti.
6) Pagina 404 carina
7) Controllare che input piccoli o grandi (1 lettera, 35 lettere) non distruggano la pagina vetrina



------- COSE DA FARE:
1) CSS: Mobile e print
2) Database (MariaDB)
    - Strutturare DB
    - Diagramma ER



------- COSE DA FARE DOPO:
1) PHP
2) Javascript
    - Controlli/protezioni per campi non compilati
    - 





NB PHP:
- Per i form testare che cambiando i parametri passati in get non dia errore.
- Aggiugnere come campi a DB anche l'alt delle immagini (ad es. sui prodotti).
- Controllare quando non c'è connessione o quando non viene restituito niente.
    -> Dare output capibile dall'utente.
- Consiglio -> Uso i vari segnaposti.
- Testare errori di validazioni, bisogna ritornare la lista degli errori e deve essere una lista (ul -> li) valida e accessivile quindi se non ci sono errori non buttare fuori la lista in output.
- RIPORTARE L'INPUT CHE AVEVA DATO L'UTENTE IN CASO DI ERRORE.
