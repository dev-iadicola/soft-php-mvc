<style>
    .projects-grid {
        max-width: 1100px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .projects-grid__heading {
        font-family: var(--font-playfair);
        font-size: 1.75rem;
        font-weight: 700;
        color: #111;
        text-align: center;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
    }

    .projects-grid__rule {
        width: 60px;
        border: none;
        border-top: 2px solid #222;
        margin: 0 auto 2.5rem;
    }

    .projects-grid__list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .project-card {
        border: 1px solid #ddd;
        background: #fff;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
    }

    .project-card:hover {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .project-card__image-link {
        display: block;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #eee;
        background: #fafafa;
    }

    .project-card__image-link img {
        display: block;
        width: 100%;
        height: 200px;
        object-fit: contain;
        padding: 0.75rem;
        transition: transform 0.3s ease;
    }

    .project-card__image-link:hover img {
        transform: scale(1.03);
    }

    .project-card__overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.4);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0;
        transition: opacity 0.25s;
    }

    .project-card__image-link:hover .project-card__overlay {
        opacity: 1;
    }

    .project-card__body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .project-card__title {
        font-family: var(--font-space-grotesk);
        font-size: 1rem;
        font-weight: 700;
        color: #111;
        margin: 0 0 0.75rem;
        letter-spacing: 0.02em;
    }

    .project-card__overview {
        font-size: 0.85rem;
        line-height: 1.6;
        color: #555;
        flex: 1;
        margin-bottom: 1rem;
        border-left: 2px solid #ddd;
        padding-left: 0.75rem;
    }

    .project-card__overview * {
        color: #555;
    }

    .project-card__actions {
        display: flex;
        gap: 0.75rem;
        border-top: 1px solid #eee;
        padding-top: 0.75rem;
    }

    .project-card__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #333;
        color: #333;
        background: transparent;
        transition: background 0.2s, color 0.2s;
        letter-spacing: 0.02em;
    }

    .project-card__btn:hover {
        background: #333;
        color: #fff;
    }

    .project-card__btn--primary {
        border-color: #1a56db;
        color: #1a56db;
    }

    .project-card__btn--primary:hover {
        background: #1a56db;
        color: #fff;
    }
</style>

<section class="projects-grid fade-in-section">

    <h3 class="projects-grid__heading">Progetti</h3>
    <hr class="projects-grid__rule" />

    <div class="projects-grid__list">
        <?php foreach ($projects as $project) { ?>

            <article class="project-card">

                <a href="/progetti/<?= urlencode($project->title) ?>" class="project-card__image-link">
                    <img src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
                         alt="<?= $project->title ?>" />
                    <span class="project-card__overlay">Apri progetto</span>
                </a>

                <div class="project-card__body">
                    <h4 class="project-card__title"><?= $project->title ?></h4>

                    <div class="project-card__overview">
                        {{{ $project->overview }}}
                    </div>

                    <div class="project-card__actions">
                        <?php if (!is_null($project->link)) { ?>
                            <a href="<?= $project->link ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="project-card__btn">
                                <i class="fa fa-github" aria-hidden="true"></i> Code
                            </a>
                        <?php } ?>
                        <?php if (!is_null($project->website)) { ?>
                            <a href="<?= $project->website ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="project-card__btn project-card__btn--primary">
                                <i class="fa fa-external-link" aria-hidden="true"></i> Sito
                            </a>
                        <?php } ?>
                    </div>
                </div>

            </article>

        <?php } ?>
    </div>

</section>
