<style>
    body{
        background-color: var(--color-design) !important;
    }
    .error-container {
        background: #1e1e1e;
        border: 2px solid #ffffffff;
        border-left: 6px solid #e74c3c;
        border-radius: 10px;
        padding: 40px 60px;
        text-align: center;
        box-shadow: 0 0 20px rgba(231, 76, 60, 0.2);
        max-width: 600px;
        width: 90%;
    }

    section {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        /* altezza schermo */
       
       
    }

    .error-container h2 {
        font-size: 5rem;
        color: #ffffffff;
        margin: 0;
        text-shadow: 0 0 10px rgba(22, 18, 18, 0.5);
    }

    .error-container h3 {
        font-size: 1.5rem;
        color: #f39c12;
        margin-top: 15px;
        word-wrap: break-word;
    }

    /* Effetto animazione leggera */
    .error-container {
        animation: pop 0.3s ease-in-out;
        transition: transform 0.3s ease, box-shadow 0.3s ease;

    }

    .error-container:hover {
        transform: scale(1.02);
        box-shadow: 0 0 25px rgba(231, 76, 60, 0.3);
    }

    @keyframes pop {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Modalità chiara automatica */
    @media (prefers-color-scheme: light) {
        body {
            background: #f5f5f5;
            color: #2c3e50;
        }

        .error-container {
            background: #ffffff;
            border-color: #e74c3c;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.1);
        }

        .error-container h3 {
            color: #555;
        }
    }
</style>
<section>

    <div class="container error-container">
        <h2><?= $code ?></h2>
        <h3><?= $errorMsg ?></h3>
    </div>

</section>