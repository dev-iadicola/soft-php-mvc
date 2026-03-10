<style>
    .error-section {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }

    .error-box {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-left: 3px solid var(--accent-red);
        border-radius: var(--radius);
        padding: 2.5rem 3rem;
        text-align: center;
        max-width: 500px;
        width: 90%;
    }

    .error-box__code {
        font-family: var(--font-mono);
        font-size: 4rem;
        font-weight: 700;
        color: var(--accent-red);
        margin: 0 0 0.5rem;
        text-shadow: 0 0 20px rgba(248, 81, 73, 0.3);
    }

    .error-box__msg {
        font-family: var(--font-mono);
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin: 0;
    }
</style>

<section class="error-section">
    <div class="error-box">
        <h2 class="error-box__code"><?= $code ?></h2>
        <p class="error-box__msg">// <?= $errorMsg ?></p>
    </div>
</section>
