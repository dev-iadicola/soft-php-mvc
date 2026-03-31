<div class="container m-5">
    <h2 class="my-4"><i class="fa fa-envelope"></i> Template Email</h2>
    <p class="text-muted">Gestisci i template delle email automatiche. Usa i placeholder <code>{nome}</code>, <code>{email}</code>, <code>{messaggio}</code> nel corpo del template.</p>

    <?php if (isset($template)) : ?>
        <!-- Form modifica template -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-pencil"></i> Modifica: <?= htmlspecialchars($template->slug) ?>
            </div>
            <div class="card-body">
                <form action="/admin/email-templates/<?= $template->id ?>" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="subject"><strong>Oggetto</strong></label>
                        <input type="text" name="subject" id="subject" class="form-control" value="<?= htmlspecialchars($template->subject) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="body"><strong>Corpo (HTML)</strong></label>
                        <textarea name="body" id="body" class="form-control editor" rows="12" required><?= $template->body ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?= $template->is_active ? 'checked' : '' ?>>
                        <label for="is_active" class="form-check-label">Attivo</label>
                        <small class="form-text text-muted d-block">Se disattivato, l'email automatica non verrà inviata.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salva</button>
                        <a href="/admin/email-templates" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>

        </div>
    <?php endif; ?>

    <!-- Lista template -->
    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Template disponibili
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Slug</th>
                        <th>Oggetto</th>
                        <th>Stato</th>
                        <th>Ultima modifica</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates ?? [] as $tpl) : ?>
                        <tr>
                            <td><code><?= htmlspecialchars($tpl->slug) ?></code></td>
                            <td><?= htmlspecialchars($tpl->subject) ?></td>
                            <td>
                                <?php if ($tpl->is_active) : ?>
                                    <span class="badge badge-success">Attivo</span>
                                <?php else : ?>
                                    <span class="badge badge-secondary">Disattivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $tpl->updated_at ? date('d/m/Y H:i', strtotime($tpl->updated_at)) : '—' ?></td>
                            <td>
                                <a href="/admin/email-templates/<?= $tpl->id ?>/edit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-pencil"></i> Modifica
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($templates)) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nessun template configurato. Esegui la migration e il seeder.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (isset($template)) : ?>
<style>
.placeholder-menu {
    display: none;
    position: absolute;
    z-index: 1060;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 220px;
    overflow: hidden;
}
.placeholder-menu.open { display: block; }
.placeholder-menu__header {
    padding: 6px 12px;
    font-size: 0.75rem;
    color: #6c757d;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}
.placeholder-menu__item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.85rem;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}
.placeholder-menu__item:hover,
.placeholder-menu__item.active {
    background: #e9ecef;
}
.placeholder-menu__item code {
    color: #d63384;
    font-size: 0.8rem;
}
.placeholder-menu__item span {
    color: #6c757d;
    font-size: 0.75rem;
}
</style>

<div id="placeholder-menu" class="placeholder-menu">
    <div class="placeholder-menu__header">Placeholder disponibili — digita <kbd>/</kbd></div>
    <button class="placeholder-menu__item" data-placeholder="{nome}">
        <code>{nome}</code> <span>Nome del mittente</span>
    </button>
    <button class="placeholder-menu__item" data-placeholder="{email}">
        <code>{email}</code> <span>Email del mittente</span>
    </button>
    <button class="placeholder-menu__item" data-placeholder="{messaggio}">
        <code>{messaggio}</code> <span>Testo del messaggio</span>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var menu = document.getElementById('placeholder-menu');
    if (!menu) return;

    var items = menu.querySelectorAll('.placeholder-menu__item');
    var activeIndex = 0;
    var quillInstance = null;
    var slashIndex = null;

    // Find the Quill instance for the body editor
    function getQuill() {
        if (quillInstance) return quillInstance;
        var wrapper = document.querySelector('#body + .quill-editor');
        if (wrapper && wrapper.__quill) {
            quillInstance = wrapper.__quill;
        }
        return quillInstance;
    }

    function showMenu() {
        var quill = getQuill();
        if (!quill) return;

        var bounds = quill.getBounds(quill.getSelection().index);
        var editorRect = quill.container.getBoundingClientRect();

        menu.style.top = (editorRect.top + bounds.top + bounds.height + window.scrollY + 4) + 'px';
        menu.style.left = (editorRect.left + bounds.left) + 'px';
        menu.classList.add('open');
        activeIndex = 0;
        updateActive();
    }

    function hideMenu() {
        menu.classList.remove('open');
        slashIndex = null;
    }

    function updateActive() {
        items.forEach(function(item, i) {
            item.classList.toggle('active', i === activeIndex);
        });
    }

    function insertPlaceholder(placeholder) {
        var quill = getQuill();
        if (!quill || slashIndex === null) return;

        // Delete the "/" character
        quill.deleteText(slashIndex, 1);
        quill.insertText(slashIndex, placeholder);
        quill.setSelection(slashIndex + placeholder.length);
        hideMenu();
    }

    // Listen for text changes in Quill
    var checkInterval = setInterval(function() {
        var quill = getQuill();
        if (!quill) return;
        clearInterval(checkInterval);

        quill.on('text-change', function(delta) {
            if (!delta.ops) return;

            // Check if a "/" was just typed
            var lastOp = delta.ops[delta.ops.length - 1];
            if (lastOp && lastOp.insert === '/') {
                var sel = quill.getSelection();
                if (sel) {
                    slashIndex = sel.index - 1;
                    showMenu();
                }
            } else if (menu.classList.contains('open')) {
                // Hide if user types something else
                if (lastOp && typeof lastOp.insert === 'string' && lastOp.insert !== '/') {
                    hideMenu();
                }
                if (lastOp && lastOp.delete) {
                    hideMenu();
                }
            }
        });
    }, 200);

    // Click on menu item
    items.forEach(function(item) {
        item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            insertPlaceholder(item.getAttribute('data-placeholder'));
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!menu.classList.contains('open')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            updateActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            updateActive();
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            insertPlaceholder(items[activeIndex].getAttribute('data-placeholder'));
        } else if (e.key === 'Escape') {
            hideMenu();
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!menu.contains(e.target)) {
            hideMenu();
        }
    });
});
</script>
<?php endif; ?>
