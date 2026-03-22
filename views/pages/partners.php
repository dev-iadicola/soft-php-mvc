<style>
    .partners-section {
        margin: 2rem 0;
    }

    .partners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }

    .partner-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem;
        transition: transform 0.2s, border-color 0.2s;
    }

    .partner-card:hover {
        transform: translateY(-3px);
        border-color: var(--accent-blue);
    }

    .partner-card__tag {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--accent-blue);
        margin-bottom: 0.5rem;
    }

    .partner-card__title {
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .partner-card__link {
        font-family: var(--font-mono);
        font-size: 0.78rem;
        color: var(--accent-green);
        text-decoration: none;
    }
</style>

<section class="partners-section fade-in-section">
    <div class="projects-section__header">
        <span class="projects-section__tag">// partners</span>
        <h1 class="projects-section__title">Partner e collaborazioni</h1>
    </div>

    <?php if (($partners ?? []) === []) : ?>
        <p class="text-muted">Nessun partner pubblicato al momento.</p>
    <?php else : ?>
        <div class="partners-grid">
            <?php foreach ($partners as $partner) : ?>
                <article class="partner-card">
                    <div class="partner-card__tag">collaboration</div>
                    <h2 class="partner-card__title"><?= $partner->name ?></h2>
                    <?php if (!empty($partner->website)) : ?>
                        <a class="partner-card__link" href="<?= $partner->website ?>" target="_blank" rel="noopener noreferrer">
                            visita il sito <i class="fa fa-external-link" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
