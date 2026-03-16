<style>
    .site-footer {
        background: var(--bg-secondary);
        border-top: 1px solid var(--border);
        padding: 2rem 0 1.5rem;
        margin-top: 3rem;
    }

    .site-footer__nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.3rem 1rem;
        list-style: none;
        padding: 0;
        margin: 0 0 1.2rem;
    }

    .site-footer__nav a {
        font-family: var(--font-mono);
        font-size: 0.75rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
    }

    .site-footer__nav a:hover {
        color: var(--accent-green);
    }

    .site-footer__social {
        display: flex;
        justify-content: center;
        gap: 0.6rem;
        list-style: none;
        padding: 0;
        margin: 0 0 1.2rem;
    }

    .site-footer__social a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.8rem;
        font-family: var(--font-mono);
        font-size: 0.75rem;
        color: var(--text-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        text-decoration: none;
        transition: all 0.2s;
    }

    .site-footer__social a:hover {
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .site-footer__copy {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--text-muted);
        text-align: center;
        margin: 0;
    }
</style>

<footer class="site-footer">
    <?php
    try {
        $footerLinks = \App\Services\LinkFooterService::getAll();
    } catch (\Throwable) {
        $footerLinks = [];
    }
    ?>
    <div class="container">
        <ul class="site-footer__nav">
            <?php foreach ($footerLinks as $footerLink) : ?>
                <li><a href="<?= $footerLink->link ?>"><?= '~/'.strtolower(str_replace(' ', '-', $footerLink->title)) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <ul class="site-footer__social">
            <li>
                <a href="https://www.linkedin.com/in/luigi-iadicola/" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-linkedin" aria-hidden="true"></i> linkedin
                </a>
            </li>
            <li>
                <a href="https://github.com/dev-iadicola/" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-github" aria-hidden="true"></i> github
                </a>
            </li>
        </ul>

        <p class="site-footer__copy">&copy; 2022-{{ date('Y') }} luigi.iadicola // software developer</p>
    </div>
</footer>
