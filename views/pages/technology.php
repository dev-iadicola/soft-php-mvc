<style>
    .tech-section {
        margin: 2rem 0;
    }

    .tech-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.9rem;
    }

    .tech-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        font-family: var(--font-mono);
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .tech-card__icon {
        font-size: 1.6rem;
        width: 28px;
        text-align: center;
        flex-shrink: 0;
        color: var(--accent-green);
    }

    .tech-card__dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--accent-green);
        box-shadow: 0 0 8px var(--glow-green);
        flex-shrink: 0;
    }
</style>

<section class="tech-section fade-in-section">
    <div class="projects-section__header">
        <span class="projects-section__tag">// stack</span>
        <h1 class="projects-section__title">Tech Stack</h1>
    </div>

    <p class="text-muted mb-4">Tecnologie usate nei progetti e nelle collaborazioni professionali.</p>

    <div class="tech-grid">
        <?php foreach (($technologies ?? []) as $technology) : ?>
            <article class="tech-card">
                <?php if (!empty($technology->icon)) : ?>
                    <i class="tech-card__icon <?= htmlspecialchars($technology->icon) ?>"></i>
                <?php else : ?>
                    <span class="tech-card__dot"></span>
                <?php endif; ?>
                <span><?= $technology->name ?></span>
            </article>
        <?php endforeach; ?>
    </div>
</section>
