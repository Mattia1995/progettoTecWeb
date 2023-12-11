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
11) Va bene la gestione per firefox per l'hover dei bottoni nella lista degli articoli?
12) Va bene la gestione dei DT e DL nella pagina delle richieste?



------- COSE DA CONTROLLARE PAGINE PROGETTO:
1) Validare le pagine sia parte html che parte CSS.
2) Aggiungere sempre Keywords e description.
3) Aggiungere aiuti alla navigazione in tutte le pagine.
4) Verificare che non ci siano link rotti (toglie punti).
5) Fare dei test con gli strumenti indicati dalla prof (slide 96 accessibilità) -> Total validator da usare in lab, la prof usa questo per validare i progetti.