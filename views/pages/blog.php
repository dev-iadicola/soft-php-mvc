<style>
    .blog-section { margin: 2rem 0; }
    .blog-section__header { margin-bottom: 1.5rem; }
    .blog-section__tag { font-family: var(--font-mono); font-size: 0.75rem; color: var(--accent-blue); }
    .blog-section__title { color: var(--text-primary); margin-top: 0.3rem; }

    .blog-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .blog-filters__search {
        flex: 1;
        min-width: 200px;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        color: var(--text-primary);
        padding: 0.5rem 0.8rem;
        font-family: var(--font-mono);
        font-size: 0.85rem;
    }
    .blog-filters__search::placeholder { color: var(--text-muted); }
    .blog-filters__btn {
        background: var(--accent-blue);
        color: #fff;
        border: none;
        border-radius: var(--radius);
        padding: 0.5rem 1rem;
        font-family: var(--font-mono);
        font-size: 0.8rem;
        cursor: pointer;
    }

    .blog-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1.5rem; }
    .blog-tag {
        font-family: var(--font-mono);
        font-size: 0.72rem;
        padding: 0.25rem 0.6rem;
        border-radius: 3px;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        text-decoration: none;
        transition: border-color 0.2s, color 0.2s;
    }
    .blog-tag:hover, .blog-tag--active {
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.2rem;
    }

    .blog-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem;
        transition: transform 0.2s, border-color 0.2s;
    }
    .blog-card:hover {
        transform: translateY(-3px);
        border-color: var(--accent-blue);
    }
    .blog-card__tag { font-family: var(--font-mono); font-size: 0.7rem; color: var(--accent-green); margin-bottom: 0.3rem; }
    .blog-card__title { color: var(--text-primary); margin-bottom: 0.4rem; font-size: 1.05rem; }
    .blog-card__subtitle { color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.5rem; }
    .blog-card__excerpt { color: var(--text-secondary); font-size: 0.8rem; line-height: 1.5; margin-bottom: 0.6rem; }
    .blog-card__meta { font-family: var(--font-mono); font-size: 0.7rem; color: var(--text-muted); }
    .blog-card__tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.5rem; }
    .blog-card__chip {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
        border-radius: 2px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        color: var(--accent-blue);
        text-decoration: none;
    }
    .blog-card__chip:hover { border-color: var(--accent-blue); }

    .blog-empty { text-align: center; padding: 3rem 0; color: var(--text-muted); font-family: var(--font-mono); }
</style>

<section class="blog-section fade-in-section">
    <div class="blog-section__header">
        <span class="blog-section__tag">// blog</span>
        <h1 class="blog-section__title">Articoli</h1>
    </div>

    <!-- Search -->
    <form method="GET" action="/blog" class="blog-filters">
        <input type="text" name="search" class="blog-filters__search" placeholder="$ grep -i 'cerca articolo...'" value="<?= htmlspecialchars($search ?? '') ?>">
        <?php if (!empty($tag)) : ?>
            <input type="hidden" name="tag" value="<?= htmlspecialchars($tag) ?>">
        <?php endif; ?>
        <button type="submit" class="blog-filters__btn">Cerca</button>
        <?php if (!empty($search) || !empty($tag)) : ?>
            <a href="/blog" class="blog-filters__btn" style="background: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border);">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Tags -->
    <?php if (!empty($tags)) : ?>
        <div class="blog-tags">
            <?php foreach ($tags as $t) : ?>
                <a href="/blog?tag=<?= urlencode($t->slug) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                   class="blog-tag <?= ($tag ?? '') === $t->slug ? 'blog-tag--active' : '' ?>">
                    #<?= htmlspecialchars($t->name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Articles Grid -->
    <?php if ($pagination->items === []) : ?>
        <div class="blog-empty">
            <p>Nessun articolo trovato.</p>
        </div>
    <?php else : ?>
        <div class="blog-grid">
            <?php foreach ($pagination->items as $article) : ?>
                <article class="blog-card">
                    <div class="blog-card__tag">article</div>
                    <h3 class="blog-card__title"><?= htmlspecialchars($article->title) ?></h3>
                    <?php if (!empty($article->subtitle)) : ?>
                        <p class="blog-card__subtitle"><?= htmlspecialchars($article->subtitle) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($article->overview)) : ?>
                        <div class="blog-card__excerpt"><?= mb_substr(strip_tags($article->overview), 0, 150) ?>...</div>
                    <?php endif; ?>
                    <div class="blog-card__meta">
                        <?= date('d/m/Y', strtotime($article->created_at)) ?>
                        <?php if (!empty($article->link)) : ?>
                            · <a href="<?= htmlspecialchars($article->link) ?>" target="_blank" rel="noopener" style="color: var(--accent-green);">Leggi <i class="fa fa-external-link"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php
                        $articleTags = \App\Services\TagService::getForArticle((int) $article->id);
                        if ($articleTags !== []) :
                    ?>
                        <div class="blog-card__tags">
                            <?php foreach ($articleTags as $at) : ?>
                                <a href="/blog?tag=<?= urlencode($at->slug) ?>" class="blog-card__chip">#<?= htmlspecialchars($at->name) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php $paginationData = $pagination; ?>
        <?php $pagination = $paginationData; ?>
        @include('components.pagination')
    <?php endif; ?>
</section>
