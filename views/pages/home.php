<style>
    .hero-terminal {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .hero-terminal__bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
    }

    .hero-terminal__dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .hero-terminal__dot--red { background: var(--accent-red); }
    .hero-terminal__dot--yellow { background: var(--accent-orange); }
    .hero-terminal__dot--green { background: var(--accent-green); }

    .hero-terminal__bar-title {
        margin-left: 0.5rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .hero-terminal__body {
        padding: 1.5rem;
    }

    .hero-terminal__prompt {
        color: var(--accent-green);
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }

    .hero-terminal__name {
        font-family: var(--font-display);
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }

    .hero-terminal__name span {
        color: var(--accent-blue);
    }

    .hero-terminal__output {
        color: var(--text-secondary);
        font-size: 0.9rem;
        min-height: 3rem;
    }

    .hero-terminal__cursor {
        color: var(--accent-green);
        animation: blink 1s step-end infinite;
    }

    @keyframes blink {
        50% { opacity: 0; }
    }

    .skills-grid {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .skills-grid__header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid var(--border);
    }

    .skills-grid__tag {
        color: var(--accent-purple);
        font-size: 0.75rem;
    }

    .skills-grid__title {
        font-size: 1.1rem;
        color: var(--text-primary);
        margin: 0;
    }

    .skills-grid__list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .skill-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        border-left: 3px solid var(--accent-green);
        transition: border-color 0.2s;
    }

    .skill-item:hover {
        border-left-color: var(--accent-blue);
    }

    .skill-item h2 {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
        color: var(--text-primary);
    }

    .skill-item p {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin: 0;
    }
</style>

<article class="fade-in-section">
    <?php foreach ($profiles as $profile): ?>
        <div class="hero-terminal">
            <div class="hero-terminal__bar">
                <span class="hero-terminal__dot hero-terminal__dot--red"></span>
                <span class="hero-terminal__dot hero-terminal__dot--yellow"></span>
                <span class="hero-terminal__dot hero-terminal__dot--green"></span>
            </div>
            <div class="hero-terminal__body">
                <?php if (!empty($profile->avatar)) : ?>
                    <img src="<?= $profile->avatar ?>" alt="<?= $profile->name ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--accent-green); margin-bottom: 0.8rem; object-fit: cover;">
                <?php endif; ?>
                <div class="hero-terminal__prompt">$ whoami</div>
                <h1 class="hero-terminal__name" id="name">
                    <span>&gt;</span> <?= strtoupper($profile->name) ?>
                </h1>
                <div class="hero-terminal__output">
                    <span id="tagline"><?= $profile->tagline ?></span>
                    <span id="welcome"><?= $profile->welcome_message ?></span>
                    <span id="cursor-text" class="hero-terminal__cursor">|</span>
                </div>
                <?php if (!empty($profile->github_url) || !empty($profile->linkedin_url) || !empty($profile->twitter_url)) : ?>
                    <div style="display: flex; gap: 0.8rem; margin-top: 0.8rem;">
                        <?php if (!empty($profile->github_url)) : ?>
                            <a href="<?= htmlspecialchars($profile->github_url) ?>" target="_blank" rel="noopener" style="color: var(--text-secondary); font-size: 1.2rem;" title="GitHub"><i class="fa fa-github"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($profile->linkedin_url)) : ?>
                            <a href="<?= htmlspecialchars($profile->linkedin_url) ?>" target="_blank" rel="noopener" style="color: var(--accent-blue); font-size: 1.2rem;" title="LinkedIn"><i class="fa fa-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($profile->twitter_url)) : ?>
                            <a href="<?= htmlspecialchars($profile->twitter_url) ?>" target="_blank" rel="noopener" style="color: var(--accent-blue); font-size: 1.2rem;" title="Twitter"><i class="fa fa-twitter"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</article>

@include('pages.portfolio')

<div class="skills-grid fade-in-section">
    <div class="skills-grid__header">
        <span class="skills-grid__tag">// skills</span>
        <h3 class="skills-grid__title">Competenze</h3>
    </div>
    <?php $activeProfile = $profiles[0] ?? null; ?>
    <?php if ($activeProfile?->bio) : ?>
        <div style="color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.85rem;">
            <?= $activeProfile->bio ?>
        </div>
    <?php endif; ?>
    <div class="skills-grid__list">
        <?php foreach ($skills as $skill): ?>
            <article class="skill-item">
                <h2><?= $skill->title ?></h2>
                <p><?= $skill->description ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</div>

@include('pages.contatti')

<a class="fa fa-arrow-up btn-arrow" id="btn-arrow" href="#top" aria-hidden="true"></a>
<script src="<?= assets('js/typewrite.js') ?>"></script>
