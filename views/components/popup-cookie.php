<style>
    .cookie-popup {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--bg-secondary);
        border-top: 1px solid var(--border);
        padding: 1rem 1.5rem;
        display: none;
        z-index: 1000;
        text-align: center;
    }

    .cookie-popup__inner {
        max-width: 700px;
        margin: 0 auto;
    }

    .cookie-popup__title {
        font-family: var(--font-mono);
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--accent-orange);
        margin-bottom: 0.3rem;
    }

    .cookie-popup__text {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    .cookie-popup__text a {
        color: var(--accent-blue);
    }

    .cookie-popup__buttons {
        display: flex;
        justify-content: center;
        gap: 0.6rem;
    }

    .cookie-popup__btn {
        font-family: var(--font-mono);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 1rem;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
    }

    .cookie-popup__btn--accept {
        background: var(--accent-green);
        color: var(--bg-primary);
        border: none;
    }

    .cookie-popup__btn--accept:hover {
        box-shadow: 0 0 12px var(--glow-green);
    }

    .cookie-popup__btn--reject {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .cookie-popup__btn--reject:hover {
        border-color: var(--accent-red);
        color: var(--accent-red);
    }

    @media (max-width: 600px) {
        .cookie-popup__buttons {
            flex-direction: column;
        }

        .cookie-popup__btn {
            width: 100%;
            padding: 0.6rem;
        }
    }
</style>

<div id="cookie-consent-popup" class="cookie-popup">
    <div class="cookie-popup__inner">
        <div class="cookie-popup__title">// cookie-policy</div>
        <p class="cookie-popup__text">
            Utilizziamo cookie per migliorare la tua esperienza.
            Consulta la nostra <a href="/laws" target="_blank">Cookie Policy</a>.
        </p>
        <div class="cookie-popup__buttons">
            <button id="accept-cookies" class="cookie-popup__btn cookie-popup__btn--accept">accept()</button>
            <button id="reject-cookies" class="cookie-popup__btn cookie-popup__btn--reject">reject()</button>
        </div>
    </div>
</div>

<script>
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('cookie-consent-popup');
        const accept = document.getElementById('accept-cookies');
        const reject = document.getElementById('reject-cookies');

        if (!getCookie('cookie-consent')) {
            popup.style.display = 'block';
        }

        accept.addEventListener('click', () => {
            setCookie('cookie-consent', 'accepted', 365);
            popup.style.display = 'none';
        });

        reject.addEventListener('click', () => {
            setCookie('cookie-consent', 'rejected', 365);
            popup.style.display = 'none';
        });
    });
</script>
