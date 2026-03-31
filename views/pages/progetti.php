<style>
    .projects-section {
        margin-bottom: 2rem;
    }

    .projects-section__header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .projects-section__tag {
        color: var(--accent-orange);
        font-size: 0.75rem;
    }

    .projects-section__title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
    }

    .projects-section__grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.2rem;
    }

    .proj-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
    }

    .proj-card:hover {
        border-color: var(--accent-blue);
        box-shadow: 0 4px 24px rgba(88, 166, 255, 0.08), 0 0 0 1px rgba(88, 166, 255, 0.1);
        transform: translateY(-3px);
    }

    /* Tab bar */
    .proj-card__tab {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.8rem;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
        font-family: var(--font-mono);
        font-size: 0.65rem;
        color: var(--text-muted);
    }

    .proj-card__tab-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .proj-card__tab-dot--red { background: #f85149; }
    .proj-card__tab-dot--yellow { background: #d29922; }
    .proj-card__tab-dot--green { background: #3fb950; }

    .proj-card__tab-name {
        margin-left: 0.3rem;
        color: var(--text-secondary);
    }

    /* Image */
    .proj-card__img-link {
        display: block;
        position: relative;
        overflow: hidden;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
    }

    .proj-card__img-link img {
        display: block;
        width: 100%;
        height: 200px;
        object-fit: contain;
        padding: 0.75rem;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .proj-card__img-link:hover img {
        transform: scale(1.05);
        filter: brightness(0.8);
    }

    .proj-card__overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 17, 23, 0.75);
        color: var(--accent-green);
        font-family: var(--font-mono);
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        opacity: 0;
        transition: opacity 0.25s;
    }

    .proj-card__img-link:hover .proj-card__overlay {
        opacity: 1;
    }

    /* Body */
    .proj-card__body {
        padding: 1rem 1.2rem 1.2rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .proj-card__title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.4rem;
    }

    /* Tech badge */
    .proj-card__tech {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        background: rgba(188, 140, 255, 0.1);
        color: var(--accent-purple);
        border: 1px solid rgba(188, 140, 255, 0.2);
        width: fit-content;
        margin-bottom: 0.6rem;
    }

    .proj-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.8rem;
    }

    .proj-card__partner {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        background: rgba(88, 166, 255, 0.1);
        color: var(--accent-blue);
        border: 1px solid rgba(88, 166, 255, 0.2);
        width: fit-content;
    }

    .proj-card__overview,
    .proj-card__overview * {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.6;
        flex: 1;
        margin-bottom: 0.8rem;
    }

    /* Status line */
    .proj-card__status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-family: var(--font-mono);
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-bottom: 0.8rem;
    }

    .proj-card__status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--accent-green);
        box-shadow: 0 0 6px var(--glow-green);
    }

    .proj-card__status-text {
        color: var(--accent-green);
        font-weight: 600;
    }

    /* Actions */
    .proj-card__actions {
        display: flex;
        gap: 0.6rem;
        padding-top: 0.8rem;
        border-top: 1px solid var(--border);
    }

    .proj-card__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--accent-green);
        border-radius: var(--radius);
        color: var(--accent-green);
        background: rgba(63, 185, 80, 0.12);
        transition: all 0.2s;
    }

    .proj-card__btn:hover {
        border-color: var(--accent-green);
        color: var(--accent-green);
        background: rgba(63, 185, 80, 0.3);
        box-shadow: 0 0 10px var(--glow-green);
    }

    .proj-card__btn--primary {
        border-color: var(--accent-blue);
        color: var(--bg-primary);
        background: var(--accent-blue);
    }

    .proj-card__btn--primary:hover {
        background: rgba(88, 166, 255, 0.85);
        color: var(--bg-primary);
        box-shadow: 0 0 14px var(--glow-blue);
    }

    .proj-card__btn--detail {
        margin-left: auto;
        border-color: var(--accent-orange);
        color: var(--accent-orange);
        background: rgba(210, 153, 34, 0.12);
    }

    .proj-card__btn--detail:hover {
        background: rgba(210, 153, 34, 0.3);
        color: var(--accent-orange);
        border-color: var(--accent-orange);
        box-shadow: 0 0 10px rgba(210, 153, 34, 0.3);
    }
</style>

<section class="projects-section fade-in-section">
    <div class="projects-section__header">
        <span class="projects-section__tag">// projects</span>
        <h3 class="projects-section__title">Progetti</h3>
    </div>

    <?php if (isset($technologies) && $technologies !== []) : ?>
        <div class="mb-4 d-flex flex-wrap gap-2">
            <a href="/progetti" class="proj-card__btn <?= empty($selectedTechnology) ? 'proj-card__btn--primary' : '' ?>">tutti</a>
            <?php foreach ($technologies as $technology) : ?>
                <a href="/progetti?technology=<?= urlencode($technology->name) ?>" class="proj-card__btn <?= ($selectedTechnology ?? null) === $technology->name ? 'proj-card__btn--primary' : '' ?>">
                    <?= $technology->name ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="projects-section__grid">
        <?php foreach ($projects as $project) { ?>
            <article class="proj-card">
                <div class="proj-card__tab">
                    <span class="proj-card__tab-dot proj-card__tab-dot--red"></span>
                    <span class="proj-card__tab-dot proj-card__tab-dot--yellow"></span>
                    <span class="proj-card__tab-dot proj-card__tab-dot--green"></span>
                    <span class="proj-card__tab-name"><?= $project->title ?></span>
                </div>

                <a href="/progetti/<?= $project->slug ?? urlencode($project->title) ?>" class="proj-card__img-link">
                    <img src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
                         alt="<?= $project->title ?>" />
                    <span class="proj-card__overlay">$ open --project</span>
                </a>

                <div class="proj-card__body">
                    <h4 class="proj-card__title"><?= $project->title ?></h4>

                    <div class="proj-card__meta">
                        <?php $projectTechnologies = $project->technologies(); ?>
                        <?php foreach ($projectTechnologies as $technology) : ?>
                            <span class="proj-card__tech">
                                <i class="fa fa-code" aria-hidden="true"></i>
                                <?= $technology->name ?>
                            </span>
                        <?php endforeach; ?>

                        <?php if ($project->partner): ?>
                            <span class="proj-card__partner">
                                <i class="fa fa-handshake-o" aria-hidden="true"></i>
                                <?= $project->partner->name ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="proj-card__overview">{{{ $project->overview }}}</div>

                    <div class="proj-card__status">
                        <?php if (!is_null($project->website)): ?>
                            <span class="proj-card__status-dot"></span>
                            <span class="proj-card__status-text">online</span>
                        <?php else: ?>
                            <span class="proj-card__status-dot" style="background:var(--text-muted);box-shadow:none;"></span>
                            <span>offline</span>
                        <?php endif; ?>
                    </div>

                    <div class="proj-card__actions">
                        <?php if (!is_null($project->link)) { ?>
                            <a href="<?= $project->link ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="proj-card__btn">
                                <i class="fa fa-github" aria-hidden="true"></i> code
                            </a>
                        <?php } ?>
                        <?php if (!is_null($project->website)) { ?>
                            <a href="<?= $project->website ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="proj-card__btn proj-card__btn--primary">
                                <i class="fa fa-external-link" aria-hidden="true"></i> live
                            </a>
                        <?php } ?>
                        <a href="/progetti/<?= $project->slug ?? urlencode($project->title) ?>"
                           class="proj-card__btn proj-card__btn--detail">
                            dettagli <i class="fa fa-angle-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>
        <?php } ?>
    </div>
</section>
