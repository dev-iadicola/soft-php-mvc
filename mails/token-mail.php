<!-- token-mail.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ciao dal tuo Personale MVC,</h1>
        <p>Grazie per aver effettuato la richiesta. Per completare il processo, per favore utilizza il seguente link:</p>

        <p>
        <a href="<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/validate-pin/' . $token->token; ?>">Apri il link per Inserire una nuova password</a>

        </p>
        <p>Questo token scade il <?php echo htmlspecialchars($token->expiry_date); ?>.</p>
        <p>Se non hai richiesto questa operazione, ignora questa email.</p>
        <p>Saluti,</p>
        <p>Il team di supporto</p>
    </div>
</body>
</html>
