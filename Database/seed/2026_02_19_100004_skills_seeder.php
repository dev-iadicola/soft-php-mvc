<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('skills')
    ->row([
        'title' => 'MySQL - MariaDB',
        'description' => "<p><strong>MariaDB e MySQL</strong> sono soluzioni potenti per la gestione dei dati delle tue applicazioni. Entrambi offrono prestazioni elevate e affidabilità, consentendo di gestire grandi volumi di informazioni con facilità. Con una sintassi SQL familiare, sono facili da utilizzare e si integrano perfettamente con vari linguaggi di programmazione.</p><p>&nbsp;</p>",
    ])
    ->row([
        'title' => 'Laravel - PHP',
        'description' => "<p>Laravel è la scelta perfetta per costruire applicazioni web sicure e scalabili. Ti permette di sviluppare rapidamente progetti personalizzati, risparmiando tempo e risorse. Con Laravel, puoi facilmente adattare la tua applicazione alle esigenze del mercato, aggiungendo nuove funzionalità senza difficoltà. Inoltre, offre robusti strumenti di sicurezza per proteggere i tuoi dati. Scegliere Laravel significa investire in un futuro sostenibile e di successo per la tua applicazione.</p>",
    ])
    ->row([
        'title' => 'React',
        'description' => "<p>La tecnologia React consente di creare design interattivi e reattivi, offrendo esperienze utente moderne e coinvolgenti. Con la sua architettura a componenti, è possibile sviluppare interfacce accattivanti e modulari, facilitando l'implementazione di animazioni fluide. React supporta anche design scalabili e accessibili, garantendo che le tue applicazioni siano belle e funzionali su tutti i dispositivi.</p>",
    ]);
