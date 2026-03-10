<style>
    .project-detail {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .project-detail__header {
        border-bottom: 2px solid #222;
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .project-detail__title {
        font-family: var(--font-playfair);
        font-size: 2rem;
        font-weight: 700;
        color: #111;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .project-detail__image {
        border: 1px solid #ddd;
        background: #fafafa;
        padding: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .project-detail__image img {
        max-width: 100%;
        max-height: 320px;
        object-fit: contain;
    }

    .project-detail__section-title {
        font-family: var(--font-space-grotesk);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #555;
        margin-bottom: 0.5rem;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 0.25rem;
    }

    .project-detail__overview {
        background: #f5f5f0;
        border-left: 3px solid #333;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        line-height: 1.7;
        color: #333;
    }

    .project-detail__overview * {
        color: #333;
    }

    .project-detail__description {
        font-size: 0.95rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 2rem;
        text-align: left;
    }

    .project-detail__description * {
        color: #444;
    }

    .project-detail__links {
        display: flex;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
    }

    .project-detail__link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #333;
        color: #333;
        background: transparent;
        transition: background 0.2s, color 0.2s;
        letter-spacing: 0.03em;
    }

    .project-detail__link:hover {
        background: #333;
        color: #fff;
    }

    .project-detail__link--primary {
        border-color: #1a56db;
        color: #1a56db;
    }

    .project-detail__link--primary:hover {
        background: #1a56db;
        color: #fff;
    }

    .project-detail__separator {
        border: none;
        border-top: 1px solid #ccc;
        margin: 3rem 0 2rem;
    }
</style>

<?php if ($project) { ?>
<section class="project-detail">

    <header class="project-detail__header">
        <h1 class="project-detail__title"><?= $project->title ?></h1>
    </header>

    <figure class="project-detail__image">
        <img
            src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
            alt="<?= $project->title ?>"
        />
    </figure>

    <div class="project-detail__section-title">Sommario</div>
    <div class="project-detail__overview">
        {{{ $project->overview }}}
    </div>

    <div class="project-detail__section-title">Descrizione</div>
    <article class="project-detail__description">
        {{{ $project->description }}}
    </article>

    <nav class="project-detail__links">
        <?php if (!empty($project->link)) { ?>
            <a href="<?= $project->link ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="project-detail__link">
                <i class="fa fa-github"></i> Codice sorgente
            </a>
        <?php } ?>

        <?php if (urlExist($project->website)) { ?>
            <a href="<?= $project->website ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="project-detail__link project-detail__link--primary">
                <i class="fa fa-external-link"></i> Sito web
            </a>
        <?php } ?>
    </nav>

    <hr class="project-detail__separator" />
</section>
<?php } ?>

@include('pages.progetti')
