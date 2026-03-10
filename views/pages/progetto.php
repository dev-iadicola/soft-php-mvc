<style>
    .proj-detail {
        max-width: 900px;
        margin: 0 auto;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .proj-detail__bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .proj-detail__body {
        padding: 1.5rem;
    }

    .proj-detail__title {
        font-family: var(--font-display);
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 1.2rem;
    }

    .proj-detail__title span {
        color: var(--accent-green);
    }

    .proj-detail__image {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .proj-detail__image img {
        max-width: 100%;
        max-height: 320px;
        object-fit: contain;
    }

    .proj-detail__label {
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--accent-purple);
        margin-bottom: 0.4rem;
    }

    .proj-detail__overview {
        background: var(--bg-card);
        border-left: 3px solid var(--accent-green);
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0 var(--radius) var(--radius) 0;
        font-size: 0.85rem;
        line-height: 1.7;
        color: var(--text-secondary);
    }

    .proj-detail__overview * {
        color: var(--text-secondary);
    }

    .proj-detail__description {
        font-size: 0.85rem;
        line-height: 1.8;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .proj-detail__description * {
        color: var(--text-secondary);
    }

    .proj-detail__links {
        display: flex;
        gap: 0.8rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }

    .proj-detail__link {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        font-family: var(--font-mono);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-secondary);
        transition: all 0.2s;
    }

    .proj-detail__link:hover {
        border-color: var(--accent-green);
        color: var(--accent-green);
    }

    .proj-detail__link--primary {
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .proj-detail__link--primary:hover {
        background: var(--accent-blue);
        color: var(--bg-primary);
    }
</style>

<?php if ($project) { ?>
<section class="proj-detail">
    <div class="proj-detail__bar">
        <i class="fa fa-folder-open-o"></i> <?= $project->title ?> — dettaglio
    </div>
    <div class="proj-detail__body">
        <h1 class="proj-detail__title"><span>&gt;</span> <?= $project->title ?></h1>

        <figure class="proj-detail__image">
            <img src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
                 alt="<?= $project->title ?>" />
        </figure>

        <div class="proj-detail__label">// sommario</div>
        <div class="proj-detail__overview">
            {{{ $project->overview }}}
        </div>

        <div class="proj-detail__label">// descrizione</div>
        <article class="proj-detail__description">
            {{{ $project->description }}}
        </article>

        <nav class="proj-detail__links">
            <?php if (! empty($project->link)) { ?>
                <a href="<?= $project->link ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="proj-detail__link">
                    <i class="fa fa-github"></i> source
                </a>
            <?php } ?>

            <?php if (urlExist($project->website)) { ?>
                <a href="<?= $project->website ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="proj-detail__link proj-detail__link--primary">
                    <i class="fa fa-external-link"></i> live
                </a>
            <?php } ?>
        </nav>
    </div>
</section>
<?php } ?>

@include('pages.progetti')
