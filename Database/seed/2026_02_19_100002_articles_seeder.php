<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('articles')
    ->row([
        'title' => 'Presentazioni',
        'subtitle' => 'Esperienza nello sviluppo di vari progetti nel corso della mia carriera.',
        'overview' => "<p>Ho realizzato siti per la gestione industriale nell'ambito dell'Industria 4.0 e 5.0 e sistemi per la gestione di più aziende sanitarie, e gestionali complessi per aziende private. Ho iniziato il mio percorso con pagine HTML e CSS, per poi ampliare le mie competenze con JavaScript, PHP e Java.&nbsp;</p><p>&nbsp;</p><p>Inoltre, ho svolto diversi corsi professionali che hanno incrementato la mia conoscenza da developer.&nbsp;</p><p>&nbsp;</p><p>Tutt'ora mi tengo aggiornato tramite corsi, documentazioni online e sviluppo di progetti personali.&nbsp;</p><p>Ogni progetto personale è una opportunità per imparare cose nuove.&nbsp;</p><p>Sviluppo progetti full stack, il che significa che partecipo alla creazione di un progetto dall'architettura del database, alla logica di business del backend, fino alla struttura grafica del frontend.&nbsp;</p><p>Questo mi consente di essere completamente autonomo nello sviluppo.</p>",
        'img' => null,
        'link' => '',
        'created_at' => '2024-08-10 14:31:41',
    ])
    ->row([
        'title' => 'Filosofia del codice',
        'subtitle' => "Una delle cose più importanti è l'organizzazione",
        'overview' => "<p>Esempio di codice spaghetti ai massimi livelli.</p><p> </p><p>Un esempio di codice disordinato si verifica quando si inseriscono tutte le funzioni all'interno di un unico controller, come nel pattern MVC.</p><p> </p><p>Un altro caso comune è l'uso di query scritte come stringhe, nonostante da anni lo sviluppo web si basi su approcci orientati agli oggetti.</p><p> </p><p>Ho visto altri esempi di cattivo codice che non sto qui a descrivere.È facile affermare di utilizzare l'ultimo framework potente, ma è importante anche saperlo usare correttamente.</p><p>Tali problematiche sono comprensibili da chi è alle prime armi, ma non dovrebbero essere presenti in chi programma da almeno due anni.</p><p>Motivi per avere un codice ordinato:Risparmio di tempo: Un codice ben strutturato evita il problema di dover cercare a lungo una funzione specifica all'interno di un controller.</p><p> </p><p>Facilità di collaborazione: Un codice disordinato può creare problemi ai colleghi, che potrebbero avere difficoltà a comprendere e lavorare sul codice esistente.</p><p> </p><p>Chiarezza e manutenzione: Avere un codice ordinato significa non perdere tempo nel modificare parti del codice, non stressarsi nel cercare metodi e funzioni, e non dover inventare nomi complessi per metodi in classi sovraffollate.</p><p> </p><p>Un codice ben organizzato facilita anche il lavoro dei programmatori che dovranno continuare lo sviluppo del progetto.</p>",
        'img' => '/storage/images/codecode.webp',
        'link' => null,
        'created_at' => '2024-08-10 14:33:14',
    ]);
