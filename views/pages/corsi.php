<style>
    .courses-section__header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .courses-section__tag {
        color: var(--accent-cyan);
        font-size: 0.75rem;
    }

    .courses-section__title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
    }

    .courses-section__grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .course-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: border-color 0.2s;
    }

    .course-card:hover {
        border-color: var(--accent-cyan);
    }

    .course-card__badge {
        display: inline-block;
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 3px;
        background: rgba(57, 211, 83, 0.1);
        color: var(--accent-green);
        border: 1px solid rgba(57, 211, 83, 0.2);
        margin-bottom: 0.6rem;
        width: fit-content;
    }

    .course-card__title {
        font-family: var(--font-display);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
    }

    .course-card__ente {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.8rem;
    }

    .course-card__link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: auto;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--accent-blue);
        transition: color 0.2s;
    }

    .course-card__link:hover {
        color: var(--accent-cyan);
    }
</style>

<?php if (isset($certificati)): ?>
<section class="fade-in-section" style="margin-bottom: 2rem;">
    <div class="courses-section__header">
        <span class="courses-section__tag">// certificates</span>
        <h3 class="courses-section__title">Certificazioni</h3>
    </div>

    <div class="courses-section__grid">
        <?php foreach ($certificati as $certificato): ?>
            <article class="course-card">
                <span class="course-card__badge"><?= $certificato->certified ?></span>
                <h5 class="course-card__title"><?= $certificato->title ?></h5>
                <p class="course-card__ente"><?= $certificato->ente ?></p>
                <a href="<?= $certificato->link ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="course-card__link">
                    <i class="fa fa-external-link" aria-hidden="true"></i> view certificate
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
