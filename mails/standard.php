<?php 
use App\Utils\Date;
?>
<!-- token-mail.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifica dal Portfolio - <?php $subject ?> </title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }

        .message {
            border:  solid 1px black;
            padding: 20px;
            border-radius: 15px;
            background-color: rgba(0, 0, 0, 0.0.5);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Notifica email</h1>
        <p>Message: <?= $message?></p>
        <h5>data: <?=  Date::Rome('d-m-Y H:i:s') ?></h5>
        <p><strong>Messaggio:</strong></p>
        <p class="message">Specifica: <?= $messaggio ?> </p>

    </div>
</body>

</html>