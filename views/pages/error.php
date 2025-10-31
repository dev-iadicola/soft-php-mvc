<style>
    body {
        margin: 0;
        font-family: "JetBrains Mono", Consolas, monospace;
        background: #0f1419;
        color: #f8f8f2;
      
     
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

    .error-container h2 {
        font-size: 5rem;
        color: #ffffffff;
        margin: 0;
        text-shadow: 0 0 10px rgba(202, 160, 155, 0.5);
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
    }

    @keyframes pop {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
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
<section class="container error-container">
<h2><?= $code ?></h2>
<h3><?= $errorMsg ?></h3>
</section>
