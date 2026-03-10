<style>
    .timeline-section {
        max-width: 800px;
        margin: 0 auto;
    }

    .timeline-section__header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .timeline-section__tag {
        color: var(--accent-purple);
        font-size: 0.75rem;
    }

    .timeline-section__title {
        font-size: 1.2rem;
        color: var(--text-primary);
        margin: 0;
    }

    .timeline-list {
        position: relative;
        padding-left: 1.5rem;
        border-left: 2px solid var(--border);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item__dot {
        position: absolute;
        width: 10px;
        height: 10px;
        background: var(--accent-green);
        border-radius: 50%;
        left: -1.85rem;
        top: 0.5rem;
        box-shadow: 0 0 8px var(--glow-green);
    }

    .timeline-item__card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.2rem;
    }

    .timeline-item__card-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.4rem;
    }

    .timeline-item__title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .timeline-item__badge {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
        background: rgba(88, 166, 255, 0.1);
        color: var(--accent-blue);
        border: 1px solid rgba(88, 166, 255, 0.2);
        white-space: nowrap;
    }

    .timeline-item__ente {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .timeline-item__toggle {
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--accent-blue);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .timeline-item__toggle:hover {
        color: var(--accent-cyan);
    }

    .timeline-item__overview {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .timeline-item__link {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.8rem;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--accent-blue);
    }

    .timeline-item__link:hover {
        color: var(--accent-cyan);
    }
</style>

<?php if (isset($certificati)): ?>
<section class="timeline-section">
    <div class="timeline-section__header">
        <span class="timeline-section__tag">// timeline</span>
        <h3 class="timeline-section__title">Percorso formativo</h3>
    </div>

    <div class="timeline-list">
        <?php foreach ($certificati as $certificato): ?>
            <div class="timeline-item">
                <div class="timeline-item__dot"></div>
                <div class="timeline-item__card">
                    <div class="timeline-item__card-header">
                        <h5 class="timeline-item__title"><?= $certificato->title ?></h5>
                        <span class="timeline-item__badge"><?= htmlspecialchars($certificato->certified) ?></span>
                    </div>
                    <p class="timeline-item__ente"><?= $certificato->ente ?></p>

                    <button class="timeline-item__toggle"
                            onclick="document.getElementById('overview-<?= $certificato->id ?>').classList.toggle('d-none')">
                        &gt; dettagli
                    </button>

                    <div class="timeline-item__overview d-none" id="overview-<?= $certificato->id ?>">
                        <p><?= $certificato->overview ?></p>
                    </div>

                    <a href="<?= $certificato->link ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="timeline-item__link">
                        <i class="fa fa-external-link" aria-hidden="true"></i> certificate
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
