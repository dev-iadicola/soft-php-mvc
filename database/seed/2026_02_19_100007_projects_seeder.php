<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('projects')
    ->row([
        'title' => 'Personal MVC',
        'technology_id' => 1,
        'partner_id' => 1,
        'overview' => 'Framework MVC custom in PHP per lo sviluppo di applicazioni web strutturate e manutenibili.',
        'description' => "<p>Presento il mio <strong>MVC Framework</strong>, una soluzione robusta per sviluppare applicazioni web moderne. Seguendo il modello <strong>Model-View-Controller</strong>, il framework separa la logica in tre componenti interconnesse, migliorando la manutenibilità e favorendo uno sviluppo agile.</p><p> </p><p>Caratteristiche Principali</p><p> </p><p><strong>Gestione delle Richieste e Risposte</strong>: Cattura e gestisce le richieste in modo efficiente.</p>",
        'link' => 'https://github.com/dev-iadicola/portfolio-php-mvc',
        'img' => '/storage/images/1000168994.png',
        'website' => 'https://iadicola.netsons.org',
        'sort_order' => 1,
    ])
    ->row([
        'title' => 'Ecommerce library',
        'technology_id' => 2,
        'overview' => 'Ecommerce completo in Laravel con gestione sconti, ordini, mail e analisi vendite.',
        'description' => "<p>Il progetto è un ecommerce per un singolo amministratore. un ecommerce fatto in artisan. Nel progetto l'amministratore può gestire gli sconti, ordini, mail, e ricevere una analisi dei dati riguardo le vendite e le zone. Inolte è presente un sistem di recupero credenziali, e di verifica utente per i consutmers. Ho voluto creare questo progetto per mostrare le abilità di laravel: codice pulito e niente spaghetti code. Laravel permette di avere un progetto complesso ma ordinato</p>",
        'link' => 'https://github.com/AndroLuix/ecommerce-library',
        'img' => '/storage/images/1000168992.jpg',
        'website' => null,
        'sort_order' => 2,
    ])
    ->row([
        'title' => 'Shop Online',
        'technology_id' => 3,
        'overview' => 'Shop online in React con design moderno, responsive e supporto dark mode.',
        'description' => "<p>Progetto realizzato con React: shop online moderno e dinamico Questo progetto è uno shop online sviluppato utilizzando React, caratterizzato da un design moderno, intuitivo e responsivo. Oltre alla funzionalità di acquisto, l'utente ha la possibilità di cambiare tema (light/dark mode) per migliorare l'esperienza di navigazione.</p><p> </p><p>Clicca sul link per visualizzare il sito web e scoprire tutte le funzionalità!</p>",
        'link' => null,
        'img' => '/storage/images/1000168993.jpg',
        'website' => 'https://dev-iadicola.github.io/shopping-online-modern-design/',
        'sort_order' => 3,
    ])
    ->row([
        'title' => 'BISTROT',
        'technology_id' => 3,
        'overview' => 'Sito vetrina per un bistrot con animazioni Lottie e design interattivo in React.',
        'description' => "<p>Questo progetto è un sito vetrina creato per un ipotetico locale, nello specifico un bistrot, con l'obiettivo di offrire un'esperienza<i> moderna</i> e <i>coinvolgente</i>.<br><br>Il sito è stato progettato per rappresentare al meglio<strong> l'identità del bistrot</strong>, con un<strong> design aggiornato e interattivo.</strong> Sono state utilizzate librerie moderne come Lottie per integrare animazioni coinvolgenti e creare un'esperienza visiva accattivante.</p>",
        'link' => 'https://github.com/dev-iadicola/react-bistrot',
        'img' => '/storage/images/1000166792.jpg',
        'website' => 'https://bistrot-tcc.netlify.app/',
        'sort_order' => 4,
    ])
    ->row([
        'title' => 'Libreria C# - IadicolaCore',
        'technology_id' => 5,
        'overview' => 'Libreria .NET di utilità per logging, eccezioni e supporto allo sviluppo di app console.',
        'description' => "<p><strong>IadicolaCore</strong> è una libreria .NET sviluppata da <i>Luigi Iadicola</i> che raccoglie strumenti e classi di utilità per semplificare la creazione di applicazioni console e framework personali.<br>Include componenti per la gestione dei log, delle eccezioni e varie funzioni di supporto generiche per velocizzare lo sviluppo.</p><p>La libreria è pensata per offrire un insieme di strumenti comuni da riutilizzare in più progetti .NET, mantenendo il codice pulito, leggibile e modulare.</p>",
        'link' => 'https://github.com/dev-iadicola/IadicolaCore',
        'img' => '/storage/images/C_Sharp_and_Dot_NET.png',
        'website' => '',
        'sort_order' => 5,
    ])
    ->row([
        'title' => 'Introduzione LM',
        'technology_id' => 7,
        'overview' => 'Percorso di apprendimento nel Machine Learning con Python, NumPy, Pandas e Matplotlib.',
        'description' => "<h2><strong>ML_Journey</strong></h2><blockquote><p>Il mio percorso personale di apprendimento nel Machine Learning</p></blockquote><h2><strong>Descrizione</strong></h2><p>Questa repository raccoglie il mio percorso di studio e pratica nel Machine Learning.<br>Partendo dai fondamenti di <strong>Python</strong>, <strong>NumPy</strong>, <strong>Pandas</strong> e <strong>Matplotlib</strong>,<br>passerò gradualmente a modelli di <strong>Machine Learning supervisionato e non supervisionato</strong>.</p>",
        'link' => 'https://github.com/dev-iadicola/ML_Journey',
        'img' => '/storage/images/1390.jpg',
        'website' => null,
        'sort_order' => 6,
    ])
    ->row([
        'title' => 'Ice Cream',
        'technology_id' => 9,
        'overview' => 'Sito web per una gelateria con design accattivante in HTML, CSS e JavaScript.',
        'description' => '',
        'link' => 'https://github.com/dev-iadicola/ice-cream',
        'img' => '/storage/images/Screenshot 2025-12-07 141140.png',
        'website' => 'https://dev-iadicola.github.io/ice-cream/',
        'sort_order' => 7,
    ])
    ->row([
        'title' => 'Color Grading',
        'technology_id' => 4,
        'overview' => 'Tool web per generare sfumature di colore a partire da un valore esadecimale.',
        'description' => "<h2><strong>Color Grading Tool</strong></h2><p> è una semplice applicazione web che consente di generare sfumature di un colore partendo da un valore esadecimale (#HEX). Inserendo un colore in formato esadecimale e selezionando il numero di sfumature desiderato, l'applicazione mostra una lista di colori con il relativo valore HEX e RGB.</p><h2><strong>Funzionalità</strong></h2><ul><li>Inserisci un colore in formato esadecimale (#HEX)</li><li>Seleziona il numero di sfumature da 5 a 100</li></ul>",
        'link' => 'https://github.com/dev-iadicola/color-grading',
        'img' => '/storage/images/Screenshot 2025-12-07 142251.png',
        'website' => 'https://dev-iadicola.github.io/color-grading/',
        'sort_order' => 8,
    ])
    ->row([
        'title' => 'Appointment Scheduling',
        'technology_id' => 3,
        'overview' => 'App React per la gestione di appuntamenti con interfaccia intuitiva e responsive.',
        'description' => "<h2><strong>Appointment Scheduling - React App</strong></h2><p>This is a simple yet powerful Appointment Scheduling application built using React. The app allows users to schedule, view, and manage their appointments efficiently.</p><h2><strong>Features</strong></h2><ul><li><strong>User-friendly interface:</strong> Easy to use with an intuitive design.</li><li><strong>Real-time scheduling:</strong> Book, view, and cancel appointments seamlessly.</li><li><strong>Responsive design:</strong> Works on all devices.</li></ul>",
        'link' => 'https://github.com/dev-iadicola/appointment-scheduling---React',
        'img' => '/storage/images/Screenshot 2025-12-07 142523.png',
        'website' => 'https://dev-iadicola.github.io/appointment-scheduling---React/',
        'sort_order' => 9,
    ])
    ->row([
        'title' => 'Multi-Marketplace Management System',
        'technology_id' => 2,
        'partner_id' => 1,
        'overview' => 'Sistema di gestione multi-marketplace con integrazione eBay, Amazon SP-API e altri canali per sincronizzazione prodotti, stock, prezzi e ordini.',
        'description' => "<h2><strong>Multi-Marketplace Management System</strong></h2><p>Sistema ERP per la gestione multi-canale di marketplace come <strong>eBay</strong>, <strong>Amazon</strong> e altri. Sviluppato con <strong>Laravel</strong>, <strong>React/TypeScript</strong> e <strong>Filament</strong>.</p><p>Il sistema gestisce la sincronizzazione di prodotti, livelli di stock, prezzi specifici per canale e ordini, con gestione errori, stato sync e job schedulati. Architettura modulare e scalabile.</p><h2><strong>Funzionalità</strong></h2><ul><li>Integrazione con API ufficiali eBay e Amazon SP-API</li><li>Sincronizzazione prodotti, stock, prezzi e ordini</li><li>Dashboard analitica e reportistica</li><li>Gestione errori e job schedulati</li></ul>",
        'link' => null,
        'img' => null,
        'website' => null,
        'sort_order' => 10,
    ])
    ->row([
        'title' => 'MDC Project - Schindler',
        'technology_id' => 1,
        'partner_id' => 2,
        'overview' => 'Server di calcolo e reportistica per Schindler, a supporto dell\'installazione di ascensori terrestri e navali tramite modelli fisici ingegneristici.',
        'description' => "<h2><strong>MDC Project — Schindler</strong></h2><p>Contributo al progetto MDC per <strong>Schindler</strong>, focalizzato su sistemi di ascensori per installazioni terrestri e navali.</p><p>Sviluppo di funzionalità backend custom a supporto di <strong>calcoli ingegneristici basati su modelli fisici</strong>. Generazione di report tecnici e output di calcolo utilizzati per valutare la fattibilità e i requisiti di installazione.</p><h2><strong>Tecnologie</strong></h2><ul><li>PHP (vanilla e Laravel)</li><li>Docker, Portainer, Nginx Proxy Manager</li></ul>",
        'link' => null,
        'img' => null,
        'website' => null,
        'sort_order' => 11,
    ])
    ->row([
        'title' => 'Healthcare Management System - Emodial',
        'technology_id' => 7,
        'partner_id' => 4,
        'overview' => 'Sistema gestionale sanitario per Emodai, con supporto a workflow operativi e gestione dati sensibili in ambiente regolamentato.',
        'description' => "<h2><strong>Healthcare Management System — Emodial</strong></h2><p>Sistema di gestione sanitaria sviluppato per <strong>Emodai</strong> presso Green Tech Solution S.R.L.</p><p>Il sistema supporta le operazioni quotidiane e la gestione dei dati in un ambiente regolamentato, con attenzione alla sicurezza dei dati sensibili e alla conformità normativa.</p><h2><strong>Tecnologie</strong></h2><ul><li>Spring Boot</li><li>Python (data processing)</li></ul>",
        'link' => null,
        'img' => null,
        'website' => null,
        'sort_order' => 12,
    ])
    ->row([
        'title' => 'Tourism Management & Data Analysis',
        'technology_id' => 2,
        'overview' => 'Sistemi gestionali per il settore turistico con dashboard, moduli analitici e strumenti di analisi trend e dati.',
        'description' => "<h2><strong>Tourism Management & Data Analysis</strong></h2><p>Sviluppo di sistemi gestionali per il <strong>settore turistico</strong> con focus su analisi dati e business intelligence.</p><p>Il progetto include dashboard interattive, moduli analitici e strumenti per l'analisi dei trend, progettati per fornire insight data-driven e supportare le decisioni aziendali.</p><h2><strong>Tecnologie</strong></h2><ul><li>Laravel, React/TypeScript, Filament</li><li>Dashboard e reportistica</li></ul>",
        'link' => null,
        'img' => null,
        'website' => null,
        'sort_order' => 13,
    ])
    ->row([
        'title' => 'Finance & Credit Management Systems',
        'technology_id' => 1,
        'partner_id' => 3,
        'overview' => 'Sistemi di gestione finanziaria e creditizia sviluppati per Sagres S.p.A., con funzionalità custom per processi aziendali interni.',
        'description' => "<h2><strong>Finance & Credit Management Systems</strong></h2><p>Sviluppo su sistemi di gestione finanziaria esistenti per <strong>Sagres S.p.A.</strong>, con focus su <strong>credit management</strong> e workflow finanziari.</p><p>Implementazione di funzionalità custom complesse basate su requisiti di business specifici, mirate a migliorare i processi aziendali interni e l'efficienza operativa.</p><h2><strong>Tecnologie</strong></h2><ul><li>PHP (vanilla e Laravel)</li><li>Vue.js, Delphi</li><li>Docker, Portainer</li></ul>",
        'link' => null,
        'img' => null,
        'website' => null,
        'sort_order' => 14,
    ]);
