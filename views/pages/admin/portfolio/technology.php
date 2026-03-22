<style>
    .icon-preview {
        font-size: 2rem;
        min-width: 40px;
        text-align: center;
    }
    .icon-select-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .icon-select-wrapper select {
        flex: 1;
    }
</style>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <?php $url = isset($technology->id) ? "/admin/technology-update/{$technology->id}" : '/admin/technology'; ?>
            <form method="POST" action="<?= $url ?>" class="shadow-sm p-4 bg-light rounded border">
                @csrf
                <?php if (isset($technology->id)) : ?>
                    @patch
                <?php endif; ?>

                <h1 class="h3 mb-3"><?= isset($technology->id) ? 'Modifica tecnologia' : 'Nuova tecnologia' ?></h1>
                <p class="text-muted small mb-4">Gestisci lo stack tecnologico usato nelle sezioni pubbliche e nei progetti.</p>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome tecnologia</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        maxlength="100"
                        value="<?= $technology->name ?? '' ?>"
                        placeholder="Es. Laravel"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Icona</label>
                    <div class="icon-select-wrapper">
                        <div class="icon-preview" id="icon-preview">
                            <?php if (!empty($technology->icon)) : ?>
                                <i class="<?= htmlspecialchars($technology->icon) ?>"></i>
                            <?php else : ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                        <select class="form-select" id="icon" name="icon">
                            <option value="">-- Nessuna icona --</option>
                        </select>
                    </div>
                    <div class="form-text">Scegli un'icona dalla libreria Devicon. Puoi digitare nel campo per filtrare.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <?= isset($technology->id) ? 'Salva modifiche' : 'Aggiungi tecnologia' ?>
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="shadow-sm p-4 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Stack attuale</h2>
                    <span class="badge text-bg-dark"><?= count($technologies) ?> elementi</span>
                </div>

                <?php if ($technologies === []) : ?>
                    <p class="text-muted mb-0">Nessuna tecnologia configurata.</p>
                <?php else : ?>
                    <div class="list-group" id="sortable-list" data-entity="technology">
                        <?php foreach ($technologies as $item) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-3" data-id="<?= $item->id ?>" style="<?= $item->is_active ? '' : 'opacity: 0.5;' ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="drag-handle text-muted" style="cursor: grab;"><i class="fa fa-bars"></i></span>
                                    <?php if (!empty($item->icon)) : ?>
                                        <i class="<?= htmlspecialchars($item->icon) ?>" style="font-size: 1.4rem; width: 28px; text-align: center;"></i>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= $item->name ?></strong>
                                        <div class="text-muted small">ID #<?= $item->id ?></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-<?= $item->is_active ? 'success' : 'secondary' ?> btn-sm toggle-active-btn" onclick="toggleActive('technology', <?= $item->id ?>, this)"><?= $item->is_active ? 'Attivo' : 'Archiviato' ?></button>
                                    <a href="/admin/technology-edit/<?= $item->id ?>" class="btn btn-outline-primary btn-sm">Modifica</a>
                                    <form action="/admin/technology-delete/<?= $item->id ?>" method="POST" onsubmit="return confirm('Eliminare <?= $item->name ?>?');">
                                        @csrf
                                        @delete
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Elimina</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initSortable('sortable-list');

    const DEVICONS = [
        'android', 'angular', 'apache', 'apple', 'arduino', 'aws', 'azure', 'babel',
        'bash', 'bitbucket', 'bootstrap', 'c', 'cakephp', 'centos', 'chrome', 'circleci',
        'clojure', 'cmake', 'codepen', 'composer', 'confluence', 'cplusplus', 'csharp',
        'css3', 'dart', 'debian', 'digitalocean', 'django', 'docker', 'dot-net', 'dotnetcore',
        'drupal', 'electron', 'elixir', 'elm', 'ember', 'erlang', 'eslint', 'express',
        'figma', 'firebase', 'firefox', 'flask', 'flutter', 'gatsby', 'gcc', 'git',
        'github', 'gitlab', 'go', 'google', 'googlecloud', 'gradle', 'graphql', 'gulp',
        'haskell', 'heroku', 'html5', 'hugo', 'illustrator', 'intellij', 'ionic', 'java',
        'javascript', 'jeet', 'jenkins', 'jest', 'jetbrains', 'jira', 'jquery', 'julia',
        'jupyter', 'karma', 'kotlin', 'kubernetes', 'laravel', 'latex', 'less', 'linkedin',
        'linux', 'lua', 'magento', 'markdown', 'materialui', 'matlab', 'maven', 'mocha',
        'mongodb', 'moodle', 'mysql', 'nestjs', 'nextjs', 'nginx', 'nixos', 'nodejs',
        'npm', 'nuxtjs', 'objectivec', 'openal', 'opensuse', 'opera', 'oracle', 'pandas',
        'perl', 'photoshop', 'php', 'phpstorm', 'postgresql', 'postman', 'powershell',
        'premierepro', 'processing', 'prometheus', 'pycharm', 'python', 'pytorch', 'r',
        'rails', 'raspberrypi', 'react', 'redis', 'redhat', 'redux', 'ruby', 'rust',
        'safari', 'salesforce', 'sass', 'scala', 'selenium', 'sequelize', 'sketch',
        'slack', 'solidity', 'spring', 'sqlite', 'ssh', 'storybook', 'svelte', 'swift',
        'symfony', 'tailwindcss', 'terraform', 'tomcat', 'trello', 'typescript', 'ubuntu',
        'unity', 'unrealengine', 'vagrant', 'vim', 'visualstudio', 'vscode', 'vuejs',
        'webpack', 'windows8', 'wordpress', 'xcode', 'yarn', 'yii', 'zend'
    ];

    const VARIANTS = ['plain', 'original', 'line'];
    const currentValue = <?= json_encode($technology->icon ?? '') ?>;
    const select = document.getElementById('icon');
    const preview = document.getElementById('icon-preview');

    // Build all options
    DEVICONS.forEach(function (name) {
        VARIANTS.forEach(function (variant) {
            const className = 'devicon-' + name + '-' + variant;
            const option = document.createElement('option');
            option.value = className;
            option.textContent = name + ' (' + variant + ')';
            if (className === currentValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    });

    // Update preview on change
    select.addEventListener('change', function () {
        if (this.value) {
            preview.innerHTML = '<i class="' + this.value + '"></i>';
        } else {
            preview.innerHTML = '<span class="text-muted">-</span>';
        }
    });
});
</script>
