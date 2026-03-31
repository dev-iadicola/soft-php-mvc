<div class="container admin-panel mt-5 ms-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Sessioni attive</h1>
            <p class="text-muted mb-0">Controlla dove il tuo account risulta autenticato e termina le sessioni non desiderate.</p>
        </div>
        <span class="badge badge-info p-2"><?= count($sessions) ?> sessioni</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (count($sessions) === 0): ?>
                <p class="mb-0 text-muted">Nessuna sessione attiva trovata.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID sessione</th>
                                <th>IP</th>
                                <th>Browser / device</th>
                                <th>Ultima attivita</th>
                                <th>Creata il</th>
                                <th class="text-end">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td style="word-break: break-all;">
                                        <code><?= htmlspecialchars($session->id, ENT_QUOTES, 'UTF-8') ?></code>
                                        <?php if ($session->id === $currentSessionId): ?>
                                            <span class="badge badge-success ml-2">Corrente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($session->ip, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($session->user_agent ?? 'Sconosciuto', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($session->last_activity, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($session->created_at ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end">
                                        <form method="POST" action="/admin/sessions/<?= urlencode($session->id) ?>/terminate" class="d-inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Terminare questa sessione?')">
                                                Termina
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
