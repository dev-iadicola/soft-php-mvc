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

    .proj-detail__content {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem 1.4rem;
        margin-bottom: 1.5rem;
        position: relative;
        font-family: 'Georgia', 'Times New Roman', 'Noto Serif', serif;
        font-size: 0.9rem;
        line-height: 1.85;
        color: var(--text-secondary);
        letter-spacing: 0.01em;
    }

    .proj-detail__content * {
        color: var(--text-secondary);
        font-family: inherit;
    }

    .proj-detail__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .proj-detail__meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.7rem;
        font-family: var(--font-mono);
        font-size: 0.72rem;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text-secondary);
    }

    .proj-detail__overview {
        padding-bottom: 1.2rem;
        margin-bottom: 1.2rem;
        border-bottom: 1px solid var(--border);
    }

    .proj-detail__description {
        text-align: left;
    }

    .proj-detail__tts {
        position: absolute;
        top: 0.8rem;
        right: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.7rem;
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 600;
        border: 1px solid var(--accent-purple);
        border-radius: var(--radius);
        background: rgba(188, 140, 255, 0.1);
        color: var(--accent-purple);
        cursor: pointer;
        transition: all 0.2s;
    }

    .proj-detail__tts:hover {
        background: rgba(188, 140, 255, 0.25);
        box-shadow: 0 0 10px rgba(188, 140, 255, 0.3);
    }

    .proj-detail__tts--active {
        background: var(--accent-purple);
        color: var(--bg-primary);
    }

    .proj-detail__tts--active:hover {
        background: rgba(188, 140, 255, 0.85);
        color: var(--bg-primary);
    }

    .proj-detail__pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
    }

    .proj-detail__pagination[hidden] {
        display: none;
    }

    .proj-detail__pagination-status {
        font-family: var(--font-mono);
        font-size: 0.78rem;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .proj-detail__pagination-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
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
        border: 1px solid var(--accent-green);
        border-radius: var(--radius);
        background: rgba(0, 255, 65, 0.1);
        color: var(--accent-green);
        transition: all 0.2s;
    }

    .proj-detail__link:hover {
        background: var(--accent-green);
        border-color: var(--accent-green);
        color: var(--bg-primary);
        box-shadow: 0 0 12px rgba(0, 255, 65, 0.4);
    }

    .proj-detail__link--primary {
        background: var(--accent-blue);
        border-color: var(--accent-blue);
        color: var(--bg-primary);
    }

    .proj-detail__link--primary:hover {
        background: color-mix(in srgb, var(--accent-blue) 80%, white);
        color: var(--bg-primary);
        box-shadow: 0 0 12px rgba(0, 140, 255, 0.4);
    }

    @media (max-width: 640px) {
        .proj-detail__pagination {
            flex-direction: column;
            align-items: stretch;
        }

        .proj-detail__pagination-status {
            text-align: center;
        }

        .proj-detail__pagination-actions {
            justify-content: stretch;
        }

        .proj-detail__pagination-actions .proj-detail__link {
            flex: 1 1 0;
            justify-content: center;
        }
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

        <div class="proj-detail__meta">
            <?php foreach ($project->technologies() as $technology) : ?>
                <div class="proj-detail__meta-item">
                    <i class="fa fa-code" aria-hidden="true"></i>
                    <?= $technology->name ?>
                </div>
            <?php endforeach; ?>
            <?php if ($project->partner) : ?>
                <div class="proj-detail__meta-item">
                    <i class="fa fa-handshake-o" aria-hidden="true"></i>
                    <?php if (!empty($project->partner->website)) : ?>
                        <a href="<?= $project->partner->website ?>" target="_blank" rel="noopener noreferrer"><?= $project->partner->name ?></a>
                    <?php else : ?>
                        <?= $project->partner->name ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="proj-detail__content">
            <button type="button" class="proj-detail__tts" data-tts-toggle>
                <i class="fa fa-volume-up"></i> ascolta
            </button>
            <div class="proj-detail__overview">
                {{{ $project->overview }}}
            </div>
            <article class="proj-detail__description" data-project-description>
                {{{ $project->description }}}
            </article>
        </div>

        <nav class="proj-detail__pagination" aria-label="Paginazione descrizione progetto" data-project-pagination hidden>
            <div class="proj-detail__pagination-status" data-project-pagination-status></div>
            <div class="proj-detail__pagination-actions">
                <button type="button" class="proj-detail__link" data-project-pagination-prev>
                    <i class="fa fa-arrow-left"></i> precedente
                </button>
                <button type="button" class="proj-detail__link proj-detail__link--primary" data-project-pagination-next>
                    successiva <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </nav>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var description = document.querySelector('[data-project-description]');
    var pagination = document.querySelector('[data-project-pagination]');

    if (!description || !pagination) {
        return;
    }

    var status = pagination.querySelector('[data-project-pagination-status]');
    var prevButton = pagination.querySelector('[data-project-pagination-prev]');
    var nextButton = pagination.querySelector('[data-project-pagination-next]');

    if (!status || !prevButton || !nextButton) {
        return;
    }

    var maxCharsPerPage = 1200;
    var nodes = Array.prototype.slice.call(description.childNodes);

    if (nodes.length === 0) {
        return;
    }

    var pages = [];
    var currentPageNodes = [];
    var currentLength = 0;

    nodes.forEach(function (node) {
        var text = (node.textContent || '').replace(/\s+/g, ' ').trim();
        var nodeLength = text.length;

        if (!nodeLength) {
            return;
        }

        if (currentPageNodes.length > 0 && (currentLength + nodeLength) > maxCharsPerPage) {
            pages.push(currentPageNodes);
            currentPageNodes = [];
            currentLength = 0;
        }

        currentPageNodes.push(node.cloneNode(true));
        currentLength += nodeLength;

        if (currentLength >= maxCharsPerPage) {
            pages.push(currentPageNodes);
            currentPageNodes = [];
            currentLength = 0;
        }
    });

    if (currentPageNodes.length > 0) {
        pages.push(currentPageNodes);
    }

    if (pages.length <= 1) {
        return;
    }

    var currentPageIndex = 0;

    function renderPage(index) {
        description.innerHTML = '';

        pages[index].forEach(function (node) {
            description.appendChild(node.cloneNode(true));
        });

        status.textContent = 'Pagina ' + (index + 1) + ' di ' + pages.length;
        prevButton.disabled = index === 0;
        nextButton.disabled = index === (pages.length - 1);
    }

    prevButton.addEventListener('click', function () {
        if (currentPageIndex === 0) {
            return;
        }

        currentPageIndex -= 1;
        renderPage(currentPageIndex);
    });

    nextButton.addEventListener('click', function () {
        if (currentPageIndex >= pages.length - 1) {
            return;
        }

        currentPageIndex += 1;
        renderPage(currentPageIndex);
    });

    pagination.hidden = false;
    renderPage(currentPageIndex);
});

document.addEventListener('DOMContentLoaded', function () {
    var ttsButton = document.querySelector('[data-tts-toggle]');
    var content = document.querySelector('.proj-detail__content');

    if (!ttsButton || !content || !('speechSynthesis' in window)) {
        if (ttsButton) ttsButton.style.display = 'none';
        return;
    }

    var synth = window.speechSynthesis;
    var speaking = false;

    function stop() {
        synth.cancel();
        speaking = false;
        ttsButton.classList.remove('proj-detail__tts--active');
        ttsButton.innerHTML = '<i class="fa fa-volume-up"></i> ascolta';
    }

    function speak() {
        var overview = content.querySelector('.proj-detail__overview');
        var description = content.querySelector('.proj-detail__description');
        var text = '';

        if (overview) text += overview.textContent.trim();
        if (description) text += ' ' + description.textContent.trim();

        text = text.replace(/\s+/g, ' ').trim();
        if (!text) return;

        var utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'it-IT';
        utterance.rate = 0.95;

        utterance.onend = function () { stop(); };
        utterance.onerror = function () { stop(); };

        speaking = true;
        ttsButton.classList.add('proj-detail__tts--active');
        ttsButton.innerHTML = '<i class="fa fa-stop"></i> stop';
        synth.speak(utterance);
    }

    ttsButton.addEventListener('click', function () {
        if (speaking) {
            stop();
        } else {
            speak();
        }
    });
});
</script>

@include('pages.progetti')
