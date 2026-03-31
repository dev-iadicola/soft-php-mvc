<?php

declare(strict_types=1);

if (!function_exists('articleDemoCover')) {
    function articleDemoCover(string $label, string $accent, string $background): string
    {
        $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 630'>"
            . "<rect width='1200' height='630' fill='{$background}'/>"
            . "<rect x='44' y='44' width='1112' height='542' rx='26' fill='none' stroke='{$accent}' stroke-width='4'/>"
            . "<text x='84' y='122' fill='{$accent}' font-family='Arial' font-size='28'>BLOG DEMO</text>"
            . "<text x='84' y='238' fill='#f8fafc' font-family='Arial' font-size='58' font-weight='700'>{$safeLabel}</text>"
            . "<text x='84' y='300' fill='#cbd5e1' font-family='Arial' font-size='28'>Seed realistico per test editoriale</text>"
            . "</svg>";

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}

if (!function_exists('articleDemoDefinitions')) {
    /**
     * @return array<int, array{
     *     title: string,
     *     slug: string,
     *     subtitle: string,
     *     overview: string,
     *     link: ?string,
     *     created_at: string,
     *     format: string,
     *     cover_label: string,
     *     accent: string,
     *     background: string,
     *     tags: array<int, string>
     * }>
     */
    function articleDemoDefinitions(): array
    {
        return [
            [
                'title' => 'Backend PHP manutenibile senza monoliti',
                'slug' => 'backend-php-manutenibile-senza-monoliti',
                'subtitle' => 'Pillar article su struttura del codice, responsabilita chiare e service layer leggibile.',
                'overview' => '<h2>Perche questo tema conta</h2><p>Un backend PHP resta veloce da evolvere solo quando controller, service e model hanno responsabilita nette. Appena tutta la logica scivola nello stesso punto, ogni modifica diventa piu costosa.</p><p>In questo articolo raccolgo il modo in cui organizzo servizi, validazione, query e rendering, con un focus pratico sulla manutenibilita di progetti custom.</p><h2>Checklist iniziale</h2><ul><li>Controller sottili e leggibili</li><li>Service con logica di dominio esplicita</li><li>Validazione vicina agli input</li><li>Query riusabili e testabili</li></ul><p>Il risultato e un codice meno fragile, piu facile da rivedere e piu semplice da estendere senza regressioni inutili.</p>',
                'link' => 'https://github.com/dev-iadicola/portfolio-php-mvc',
                'created_at' => '2026-01-12 09:15:00',
                'format' => 'pillar',
                'cover_label' => 'Backend PHP',
                'accent' => '#22c55e',
                'background' => '#0f172a',
                'tags' => ['PHP', 'Architettura', 'Backend'],
            ],
            [
                'title' => 'Portfolio tecnico che converte',
                'slug' => 'portfolio-tecnico-che-converte',
                'subtitle' => 'Pillar article su contenuti, gerarchia visiva e pagine che trasformano visite in contatti utili.',
                'overview' => '<h2>Obiettivo reale</h2><p>Un portfolio tecnico non deve solo essere gradevole: deve spiegare competenze, far emergere il valore dei progetti e accompagnare verso il contatto senza confondere.</p><p>Per questo conviene ragionare per blocchi: prova sociale, casi studio, call to action chiare e pagine di dettaglio capaci di raccontare processo e risultati.</p><h2>Cosa misuro</h2><ul><li>Chiarezza del messaggio iniziale</li><li>Facilita nel capire stack e competenze</li><li>Presenza di prove concrete, non solo claim</li><li>Percorso semplice verso il form contatti</li></ul><p>Quando questi elementi lavorano insieme, il portfolio smette di essere una vetrina statica e diventa uno strumento commerciale credibile.</p>',
                'link' => null,
                'created_at' => '2026-01-19 11:20:00',
                'format' => 'pillar',
                'cover_label' => 'Portfolio UX',
                'accent' => '#38bdf8',
                'background' => '#082f49',
                'tags' => ['UX', 'Portfolio', 'Conversione'],
            ],
            [
                'title' => 'Query SQL veloci in un gestionale reale',
                'slug' => 'query-sql-veloci-in-un-gestionale-reale',
                'subtitle' => 'Pillar article su indici, filtri e query che reggono quando i dati iniziano a pesare davvero.',
                'overview' => '<h2>Dal prototipo alla realta</h2><p>Le query che sembrano innocue con poche righe diventano rapidamente un collo di bottiglia quando il gestionale cresce. Il punto non e usare solo SQL avanzato, ma scegliere bene indici, filtri e shape dei dati.</p><p>Nel lavoro quotidiano osservo soprattutto paginazione, join ripetute, campi cercati piu spesso e casi in cui la count totale viene calcolata in modo inefficiente.</p><h2>Interventi ad alto impatto</h2><ul><li>Indici sui campi realmente interrogati</li><li>Selezione colonne mirata invece di <code>*</code></li><li>Paginazione stabile</li><li>Riduzione delle join superflue</li></ul><p>Una query piu semplice, ben mirata, e spesso la forma di ottimizzazione piu economica che abbiamo.</p>',
                'link' => null,
                'created_at' => '2026-01-28 08:35:00',
                'format' => 'pillar',
                'cover_label' => 'SQL Performance',
                'accent' => '#f59e0b',
                'background' => '#451a03',
                'tags' => ['Database', 'Performance', 'SQL'],
            ],
            [
                'title' => 'Logging PHP utile in produzione',
                'slug' => 'logging-php-utile-in-produzione',
                'subtitle' => 'Pillar article su log leggibili, contesto minimo necessario e debugging meno rumoroso.',
                'overview' => '<h2>Il problema dei log inutili</h2><p>Loggare tutto non significa capire meglio cosa accade. In produzione servono segnali utili: eventi importanti, contesto minimo e messaggi consistenti nel tempo.</p><p>Un buon logging aiuta a isolare errori, ricostruire flussi e intervenire piu in fretta, senza sommergere il team con rumore.</p><h2>Pattern pratici</h2><ul><li>Messaggi brevi ma specifici</li><li>ID, route e actor quando servono</li><li>Separazione tra info, warning ed error</li><li>No dump arbitrari dentro ai controller</li></ul><p>Un log ben progettato non e solo un file: e uno strumento operativo che accelera supporto e manutenzione.</p>',
                'link' => null,
                'created_at' => '2026-02-02 14:05:00',
                'format' => 'pillar',
                'cover_label' => 'Logging PHP',
                'accent' => '#e879f9',
                'background' => '#3b0764',
                'tags' => ['PHP', 'Debugging', 'Produzione'],
            ],
            [
                'title' => 'Quick note: Quill in admin',
                'slug' => 'quick-note-quill-in-admin',
                'subtitle' => 'Quick note su quando un editor rich-text aiuta davvero e quando complica un form semplice.',
                'overview' => '<h2>Uso sensato</h2><p>Quill e utile quando il contenuto ha struttura editoriale reale: paragrafi, liste, quote e link. Se il campo deve raccogliere solo due righe, spesso introduce complessita inutile.</p><p>Prima di aggiungerlo in admin vale la pena chiedersi se l utente sta davvero scrivendo contenuto oppure solo compilando un dato.</p>',
                'link' => 'https://quilljs.com/',
                'created_at' => '2026-02-10 10:10:00',
                'format' => 'quick-note',
                'cover_label' => 'Quick Note',
                'accent' => '#f97316',
                'background' => '#431407',
                'tags' => ['CMS', 'UX', 'Editor'],
            ],
            [
                'title' => 'Quick note: controller troppo grande',
                'slug' => 'quick-note-controller-troppo-grande',
                'subtitle' => 'Quick note con segnali facili da riconoscere quando un controller sta assorbendo troppa logica.',
                'overview' => '<h2>Segnali da non ignorare</h2><p>Se un controller valida, salva file, compone query, prepara email e decide il redirect finale, probabilmente sta facendo troppo.</p><ul><li>Metodi molto lunghi</li><li>Troppe dipendenze indirette</li><li>Branch annidati difficili da leggere</li><li>Difficolta nel testare i casi limite</li></ul><p>Quando succede, un service ben nominato restituisce subito respiro al codice.</p>',
                'link' => null,
                'created_at' => '2026-02-14 17:30:00',
                'format' => 'quick-note',
                'cover_label' => 'Controller',
                'accent' => '#fb7185',
                'background' => '#4c0519',
                'tags' => ['Architettura', 'Refactoring', 'Backend'],
            ],
            [
                'title' => 'Da side project a case study',
                'slug' => 'da-side-project-a-case-study',
                'subtitle' => 'Pillar article su come raccontare un progetto personale in modo credibile per recruiter e clienti.',
                'overview' => '<h2>Oltre il repository</h2><p>Un side project diventa davvero spendibile quando smette di essere solo un elenco di feature e inizia a raccontare problema, scelte, trade-off e risultato.</p><p>Per questo nei case study preferisco mostrare contesto, obiettivi, stack, problemi incontrati e decisioni che dimostrano maturita tecnica.</p><h2>Struttura essenziale</h2><ul><li>Problema iniziale</li><li>Soluzione proposta</li><li>Architettura e stack</li><li>Lezioni imparate</li></ul><p>Questa forma narrativa rende un progetto molto piu leggibile e memorabile di uno screenshot isolato.</p>',
                'link' => null,
                'created_at' => '2026-02-20 09:45:00',
                'format' => 'pillar',
                'cover_label' => 'Case Study',
                'accent' => '#2dd4bf',
                'background' => '#042f2e',
                'tags' => ['Portfolio', 'Case Study', 'Full Stack'],
            ],
            [
                'title' => 'SEO tecnico per articoli e progetti',
                'slug' => 'seo-tecnico-per-articoli-e-progetti',
                'subtitle' => 'Pillar article su slug, meta, struttura delle pagine e segnali tecnici che aiutano discovery e indicizzazione.',
                'overview' => '<h2>SEO tecnico pragmatico</h2><p>Quando articoli e progetti hanno URL stabili, meta coerenti e contenuto ben strutturato, diventano piu facili da trovare e da capire sia per i motori classici sia per i sistemi di retrieval.</p><p>La parte tecnica conta soprattutto su canonical, sitemap, struttura dei titoli e qualita semantica del contenuto.</p><h2>Priorita operative</h2><ul><li>Slug chiari e stabili</li><li>Meta title e description distinti</li><li>Pagine dettaglio indicizzabili</li><li>Internal linking contestuale</li></ul><p>Non serve moltiplicare i meta tag: serve dare ai contenuti una struttura consistente e credibile.</p>',
                'link' => null,
                'created_at' => '2026-02-26 13:25:00',
                'format' => 'pillar',
                'cover_label' => 'SEO Tecnico',
                'accent' => '#a3e635',
                'background' => '#1a2e05',
                'tags' => ['SEO', 'Content Strategy', 'Discovery'],
            ],
            [
                'title' => 'Quick note: fallback immagini puliti',
                'slug' => 'quick-note-fallback-immagini-puliti',
                'subtitle' => 'Quick note su placeholder e immagini mancanti senza effetti improvvisati o UI trascurata.',
                'overview' => '<h2>Fallback che non urlano emergenza</h2><p>Un fallback grafico deve mantenere equilibrio visivo, non segnalare al volo che manca un asset. Colori coerenti, tipografia semplice e ratio costante bastano spesso piu di un placeholder generico.</p><p>Nei seed demo questo aiuta a testare davvero il comportamento delle viste anche quando non si usano immagini fotografiche reali.</p>',
                'link' => null,
                'created_at' => '2026-03-03 16:40:00',
                'format' => 'quick-note',
                'cover_label' => 'Fallback Img',
                'accent' => '#60a5fa',
                'background' => '#172554',
                'tags' => ['UX', 'Media', 'Frontend'],
            ],
            [
                'title' => 'Hardening login admin in pratica',
                'slug' => 'hardening-login-admin-in-pratica',
                'subtitle' => 'Pillar article su rate limit, 2FA e sessioni attive per alzare la sicurezza senza peggiorare troppo la UX.',
                'overview' => '<h2>Sicurezza che resta usabile</h2><p>Proteggere il login admin non significa solo bloccare tentativi: significa scegliere limiti sensati, introdurre 2FA quando serve e offrire un controllo minimo sulle sessioni attive.</p><p>Le misure migliori sono quelle che riducono il rischio senza creare un esperienza frustrante per chi deve lavorare ogni giorno nel pannello.</p><h2>Misure pratiche</h2><ul><li>Rate limit configurabile per route sensibili</li><li>Challenge TOTP dopo password valida</li><li>Sessioni attive revocabili</li><li>Messaggi di errore chiari e non ambigui</li></ul><p>La sicurezza applicativa migliora quando i dettagli sono coerenti sia lato backend sia nella UX dei form.</p>',
                'link' => null,
                'created_at' => '2026-03-12 12:00:00',
                'format' => 'pillar',
                'cover_label' => 'Login Security',
                'accent' => '#facc15',
                'background' => '#422006',
                'tags' => ['Security', 'PHP', 'Admin UX'],
            ],
        ];
    }
}
