<style>
    /* === CONTACT PAGE - BOLD STYLE === */

    /* Hero banner */
    .contact-hero {
        margin-top: 2rem;
        padding: 3rem 2rem;
        text-align: center;
        background:
            radial-gradient(ellipse at 20% 50%, rgba(63, 185, 80, 0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 50%, rgba(88, 166, 255, 0.08) 0%, transparent 50%),
            var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        position: relative;
        overflow: hidden;
    }

    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-green), var(--accent-blue), var(--accent-purple), var(--accent-orange));
    }

    .contact-hero::after {
        content: '{ }';
        position: absolute;
        bottom: 1rem;
        right: 1.5rem;
        font-family: var(--font-mono);
        font-size: 4rem;
        font-weight: 700;
        color: rgba(48, 54, 61, 0.3);
    }

    .contact-hero__badge {
        display: inline-block;
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        background: rgba(63, 185, 80, 0.1);
        color: var(--accent-green);
        border: 1px solid rgba(63, 185, 80, 0.2);
        margin-bottom: 1rem;
        letter-spacing: 0.05em;
    }

    .contact-hero__title {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.8rem;
        line-height: 1.2;
    }

    .contact-hero__title em {
        font-style: normal;
        background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .contact-hero__desc {
        font-size: 0.95rem;
        color: var(--text-secondary);
        max-width: 550px;
        margin: 0 auto 1.8rem;
        line-height: 1.8;
    }

    .contact-hero__desc strong {
        color: var(--accent-blue);
    }

    .contact-hero__stats {
        display: flex;
        justify-content: center;
        gap: 3rem;
        flex-wrap: wrap;
    }

    .contact-hero__stat {
        text-align: center;
    }

    .contact-hero__stat-value {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-green);
        display: block;
        line-height: 1;
        margin-bottom: 0.3rem;
    }

    .contact-hero__stat-label {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    /* Servizi - cards con icona grande */
    .contact-services-section {
        margin: 2rem 0;
    }

    .contact-services-section__header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .contact-services-section__tag {
        color: var(--accent-cyan);
        font-size: 0.75rem;
    }

    .contact-services-section__title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
    }

    .contact-services {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (max-width: 700px) {
        .contact-services {
            grid-template-columns: 1fr;
        }
    }

    .contact-service {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        display: flex;
        gap: 1.2rem;
        align-items: flex-start;
        transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
    }

    .contact-service:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    .contact-service:nth-child(1):hover { border-color: var(--accent-green); }
    .contact-service:nth-child(2):hover { border-color: var(--accent-blue); }
    .contact-service:nth-child(3):hover { border-color: var(--accent-purple); }
    .contact-service:nth-child(4):hover { border-color: var(--accent-orange); }

    .contact-service__icon-wrap {
        width: 3rem;
        height: 3rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .contact-service__icon-wrap--green {
        background: rgba(63, 185, 80, 0.1);
        color: var(--accent-green);
        border: 1px solid rgba(63, 185, 80, 0.2);
    }

    .contact-service__icon-wrap--blue {
        background: rgba(88, 166, 255, 0.1);
        color: var(--accent-blue);
        border: 1px solid rgba(88, 166, 255, 0.2);
    }

    .contact-service__icon-wrap--purple {
        background: rgba(188, 140, 255, 0.1);
        color: var(--accent-purple);
        border: 1px solid rgba(188, 140, 255, 0.2);
    }

    .contact-service__icon-wrap--orange {
        background: rgba(210, 153, 34, 0.1);
        color: var(--accent-orange);
        border: 1px solid rgba(210, 153, 34, 0.2);
    }

    .contact-service__content {
        flex: 1;
    }

    .contact-service__title {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
    }

    .contact-service__desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin: 0 0 0.6rem;
    }

    .contact-service__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
    }

    .contact-service__mini-tag {
        font-family: var(--font-mono);
        font-size: 0.6rem;
        padding: 0.1rem 0.4rem;
        border-radius: 3px;
        background: var(--bg-card);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }

    /* Tech stack */
    .contact-stack {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1.2rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin: 1.5rem 0;
    }

    .contact-stack__label {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-right: 0.3rem;
    }

    .contact-stack__tag {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 3px;
        background: rgba(88, 166, 255, 0.08);
        color: var(--accent-blue);
        border: 1px solid rgba(88, 166, 255, 0.15);
        transition: all 0.2s;
    }

    .contact-stack__tag:hover {
        background: rgba(88, 166, 255, 0.18);
        border-color: var(--accent-blue);
        transform: translateY(-1px);
    }

    /* Experience timeline compatta */
    .contact-exp-section {
        margin: 2rem 0;
    }

    .contact-exp-section__header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .contact-exp-section__tag {
        color: var(--accent-purple);
        font-size: 0.75rem;
    }

    .contact-exp-section__title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
    }

    .contact-exp-list {
        display: flex;
        flex-direction: column;
        gap: 0;
        border-left: 2px solid var(--border);
        padding-left: 1.5rem;
        margin-left: 0.5rem;
    }

    .contact-exp-item {
        position: relative;
        padding: 1rem 1.2rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 0.8rem;
        transition: border-color 0.2s;
    }

    .contact-exp-item:hover {
        border-color: var(--accent-green);
    }

    .contact-exp-item__dot {
        position: absolute;
        left: -2.15rem;
        top: 1.3rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--accent-green);
        box-shadow: 0 0 8px var(--glow-green);
    }

    .contact-exp-item__top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
        flex-wrap: wrap;
    }

    .contact-exp-item__company {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .contact-exp-item__date {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        background: rgba(88, 166, 255, 0.08);
        color: var(--accent-blue);
        border: 1px solid rgba(88, 166, 255, 0.15);
    }

    .contact-exp-item__role {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--accent-purple);
        margin-bottom: 0.3rem;
    }

    .contact-exp-item__desc {
        font-size: 0.78rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 0;
    }

    /* CTA */
    .contact-cta {
        background:
            radial-gradient(ellipse at 30% 50%, rgba(63, 185, 80, 0.06) 0%, transparent 60%),
            var(--bg-card);
        border: 1px solid var(--border);
        border-left: 4px solid var(--accent-green);
        border-radius: var(--radius);
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .contact-cta__icon {
        font-size: 2.5rem;
        color: var(--accent-green);
        flex-shrink: 0;
    }

    .contact-cta__text {
        flex: 1;
    }

    .contact-cta__text strong {
        font-family: var(--font-display);
        font-size: 1.1rem;
        color: var(--text-primary);
        display: block;
        margin-bottom: 0.4rem;
    }

    .contact-cta__text p {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.7;
    }

    @media (max-width: 600px) {
        .contact-cta {
            flex-direction: column;
            text-align: center;
        }
        .contact-hero__stats {
            gap: 1.5rem;
        }
        .contact-hero__title {
            font-size: 1.6rem;
        }
    }

    /* Editor form */
    .contact-editor {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .contact-editor__bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .contact-editor__body {
        padding: 2rem;
    }

    .contact-editor__title {
        font-family: var(--font-display);
        font-size: 1.4rem;
        color: var(--text-primary);
        margin-bottom: 0.4rem;
    }

    .contact-editor__hint {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .contact-editor .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 600px) {
        .contact-editor .form-row {
            grid-template-columns: 1fr;
        }
    }

    .contact-editor .form-group {
        margin-bottom: 1.2rem;
        text-align: left;
    }

    .contact-editor label {
        display: block;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--accent-purple);
        margin-bottom: 0.4rem;
    }

    .contact-editor .form-control {
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-family: var(--font-mono);
        font-size: 0.85rem;
        padding: 0.7rem 0.9rem;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .contact-editor .form-control:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px var(--glow-blue);
        background: var(--bg-input);
        color: var(--text-primary);
        outline: none;
    }

    .contact-editor .form-check {
        margin: 1.2rem 0;
    }

    .contact-editor .form-check-label {
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .contact-editor__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .contact-editor__footer-hint {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .contact-editor__submit {
        background: linear-gradient(135deg, var(--accent-green), #2ea043);
        color: var(--bg-primary);
        border: none;
        border-radius: var(--radius);
        padding: 0.8rem 2.2rem;
        font-family: var(--font-mono);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .contact-editor__submit:hover {
        box-shadow: 0 0 25px var(--glow-green), 0 4px 15px rgba(0,0,0,0.3);
        color: var(--bg-primary);
        transform: translateY(-2px);
    }

    /* Info footer */
    .contact-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .contact-info__item {
        flex: 1;
        min-width: 200px;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: border-color 0.2s, transform 0.2s;
    }

    .contact-info__item:hover {
        border-color: var(--accent-blue);
        transform: translateY(-2px);
    }

    .contact-info__icon {
        font-size: 1.2rem;
        color: var(--accent-blue);
        width: 2.4rem;
        height: 2.4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(88, 166, 255, 0.08);
        border-radius: var(--radius);
        border: 1px solid rgba(88, 166, 255, 0.15);
        flex-shrink: 0;
    }

    .contact-info__label {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .contact-info__value {
        font-size: 0.85rem;
        color: var(--text-primary);
        margin: 0;
        font-weight: 600;
    }
</style>

<!-- HERO -->
<div class="contact-hero fade-in-section">
    <span class="contact-hero__badge">// open to work</span>
    <h2 class="contact-hero__title">Costruiamo qualcosa<br><em>insieme</em></h2>
    <p class="contact-hero__desc">
        Software Engineer con esperienza in <strong>PHP</strong>, <strong>Java</strong>, <strong>React</strong> e <strong>C#</strong>.
        Ho progettato sistemi ERP, integrazioni marketplace, gestionali sanitari e piattaforme enterprise.
        Contattami per dare vita alla tua idea.
    </p>
    <div class="contact-hero__stats">
        <div class="contact-hero__stat">
            <span class="contact-hero__stat-value">3+</span>
            <span class="contact-hero__stat-label">Anni di esperienza</span>
        </div>
        <div class="contact-hero__stat">
            <span class="contact-hero__stat-value">10+</span>
            <span class="contact-hero__stat-label">Progetti realizzati</span>
        </div>
        <div class="contact-hero__stat">
            <span class="contact-hero__stat-value">5+</span>
            <span class="contact-hero__stat-label">Tecnologie</span>
        </div>
    </div>
</div>

<!-- SERVIZI -->
<section class="contact-services-section fade-in-section">
    <div class="contact-services-section__header">
        <span class="contact-services-section__tag">// cosa posso fare per te</span>
        <h3 class="contact-services-section__title">Servizi</h3>
    </div>

    <div class="contact-services">
        <div class="contact-service">
            <div class="contact-service__icon-wrap contact-service__icon-wrap--green">
                <i class="fa fa-globe" aria-hidden="true"></i>
            </div>
            <div class="contact-service__content">
                <h4 class="contact-service__title">Applicazioni Web & ERP</h4>
                <p class="contact-service__desc">Piattaforme gestionali, e-commerce, dashboard analitiche e sistemi multi-canale.</p>
                <div class="contact-service__tags">
                    <span class="contact-service__mini-tag">Laravel</span>
                    <span class="contact-service__mini-tag">React</span>
                    <span class="contact-service__mini-tag">Filament</span>
                </div>
            </div>
        </div>
        <div class="contact-service">
            <div class="contact-service__icon-wrap contact-service__icon-wrap--blue">
                <i class="fa fa-cogs" aria-hidden="true"></i>
            </div>
            <div class="contact-service__content">
                <h4 class="contact-service__title">API & Integrazioni</h4>
                <p class="contact-service__desc">API REST, integrazioni marketplace (eBay, Amazon SP-API), sincronizzazione dati.</p>
                <div class="contact-service__tags">
                    <span class="contact-service__mini-tag">REST</span>
                    <span class="contact-service__mini-tag">Spring Boot</span>
                    <span class="contact-service__mini-tag">OAuth</span>
                </div>
            </div>
        </div>
        <div class="contact-service">
            <div class="contact-service__icon-wrap contact-service__icon-wrap--purple">
                <i class="fa fa-sitemap" aria-hidden="true"></i>
            </div>
            <div class="contact-service__content">
                <h4 class="contact-service__title">Architettura Software</h4>
                <p class="contact-service__desc">Design patterns, architetture MVC, Layered, Hexagonal e SOA. Code review.</p>
                <div class="contact-service__tags">
                    <span class="contact-service__mini-tag">SOLID</span>
                    <span class="contact-service__mini-tag">Clean Code</span>
                    <span class="contact-service__mini-tag">DDD</span>
                </div>
            </div>
        </div>
        <div class="contact-service">
            <div class="contact-service__icon-wrap contact-service__icon-wrap--orange">
                <i class="fa fa-wrench" aria-hidden="true"></i>
            </div>
            <div class="contact-service__content">
                <h4 class="contact-service__title">Refactoring & DevOps</h4>
                <p class="contact-service__desc">Ottimizzazione codice, containerizzazione, CI/CD e gestione ambienti di deploy.</p>
                <div class="contact-service__tags">
                    <span class="contact-service__mini-tag">Docker</span>
                    <span class="contact-service__mini-tag">CI/CD</span>
                    <span class="contact-service__mini-tag">Git</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TECH STACK -->
<div class="contact-stack fade-in-section">
    <span class="contact-stack__label">// tech stack:</span>
    <span class="contact-stack__tag">PHP</span>
    <span class="contact-stack__tag">Laravel</span>
    <span class="contact-stack__tag">Filament</span>
    <span class="contact-stack__tag">Java</span>
    <span class="contact-stack__tag">Spring Boot</span>
    <span class="contact-stack__tag">C#</span>
    <span class="contact-stack__tag">React</span>
    <span class="contact-stack__tag">TypeScript</span>
    <span class="contact-stack__tag">Python</span>
    <span class="contact-stack__tag">PostgreSQL</span>
    <span class="contact-stack__tag">MySQL</span>
    <span class="contact-stack__tag">Docker</span>
    <span class="contact-stack__tag">REST API</span>
</div>

<!-- CTA -->
<div class="contact-cta fade-in-section">
    <span class="contact-cta__icon">
        <i class="fa fa-rocket" aria-hidden="true"></i>
    </span>
    <div class="contact-cta__text">
        <strong>Raccontami cosa hai in mente</strong>
        <p>
            Che sia un'idea da sviluppare, un problema tecnico da risolvere o una consulenza architetturale,
            descrivi la tua esigenza nel form qui sotto. Ti rispondo entro 24 ore.
        </p>
    </div>
</div>

<!-- FORM -->
<section class="contact-editor fade-in-section">
    <div class="contact-editor__bar">
        <i class="fa fa-envelope-o"></i> contatti — editor
    </div>
    <div class="contact-editor__body">
        <h2 class="contact-editor__title">Scrivimi</h2>
        <p class="contact-editor__hint">Compila tutti i campi per inviarmi un messaggio. Ti ricontatto il prima possibile.</p>

        <form action="/contatti" method="post" class="text-start">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nome">string nome <span class="text-danger">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Mario Rossi" maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="email">string email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="email@esempio.it" maxlength="100" required>
                </div>
            </div>

            <div class="form-group">
                <label for="typologie">enum typologie <span class="text-danger">*</span></label>
                <select name="typologie" id="typologie" class="form-control" required>
                    <option disabled selected value="">Seleziona tipologia</option>
                    <option value="Privato">Privato</option>
                    <option value="Azienda">Azienda</option>
                    <option value="Developer">Developer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="messaggio">text messaggio <span class="text-danger">*</span></label>
                <textarea id="messaggio" name="messaggio" class="form-control" rows="6" minlength="20" placeholder="Descrivi il tuo progetto, la tua esigenza o la tua domanda..." required></textarea>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="flexCheckDefault" required>
                <label class="form-check-label" for="flexCheckDefault">
                    Accetta <a href="/laws">Termini e Condizioni</a> <span class="text-danger">*</span>
                </label>
            </div>

            <div class="contact-editor__footer">
                <span class="contact-editor__footer-hint">// i campi con * sono obbligatori</span>
                <button type="submit" class="contact-editor__submit">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i> Invia messaggio
                </button>
            </div>
        </form>
    </div>
</section>

<!-- INFO -->
<div class="contact-info fade-in-section">
    <div class="contact-info__item">
        <span class="contact-info__icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
        <div>
            <span class="contact-info__label">Risposta</span>
            <p class="contact-info__value">Entro 24 ore</p>
        </div>
    </div>
    <div class="contact-info__item">
        <span class="contact-info__icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
        <div>
            <span class="contact-info__label">Dove</span>
            <p class="contact-info__value">Remoto / Italia</p>
        </div>
    </div>
    <div class="contact-info__item">
        <span class="contact-info__icon"><i class="fa fa-handshake-o" aria-hidden="true"></i></span>
        <div>
            <span class="contact-info__label">Modalità</span>
            <p class="contact-info__value">Freelance & Collaborazioni</p>
        </div>
    </div>
</div>
