<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Consent Example</title>
    <style>
        /* Stili per il popup di consenso ai cookie */
        .cookie-consent-popup {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
            border-top: 1px solid #ddd;
            display: none;
            z-index: 1000;
            text-align: center;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        }

        .cookie-consent-content {
            max-width: 90%;
            margin: 0 auto;
        }

        .cookie-buttons {
            margin-top: 10px;
        }

        .cookie-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .cookie-button:hover {
            background-color: #0056b3;
        }

        .cookie-consent-content a {
            color: #007bff;
            text-decoration: none;
        }

        .cookie-consent-content a:hover {
            text-decoration: underline;
        }

        /* Media query per schermi più piccoli */
        @media (max-width: 600px) {
            .cookie-consent-popup {
                padding: 15px;
            }

            .cookie-buttons {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .cookie-button {
                width: 100%;
                padding: 12px;
                margin: 5px 0;
            }
        }
        
        .cookie-consent-popup p {
            color:black;
        }
        
        
    </style>
</head>
<body>
    <!-- Cookie Consent Popup -->
    <div id="cookie-consent-popup" class="cookie-consent-popup">
        <div class="cookie-consent-content">
            <h2>Cookie Policy</h2>
            <p>We use various technologies to enhance your experience on our website. For detailed information on how we handle data and privacy, please refer to our <a href="/laws" target="_blank">Cookie Policy</a>.</p>
            <div class="cookie-buttons">
                <button id="accept-cookies" class="cookie-button">Accept All Cookies</button>
                <button id="reject-cookies" class="cookie-button">Reject Cookies</button>
            </div>
        </div>
    </div>
    
    <!-- Gestire il popup -->
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
            const consentPopup = document.getElementById('cookie-consent-popup');
            const acceptCookiesButton = document.getElementById('accept-cookies');
            const rejectCookiesButton = document.getElementById('reject-cookies');

            // Verifica il consenso dell'utente
            if (!getCookie('cookie-consent')) {
                consentPopup.style.display = 'block';
            }

            acceptCookiesButton.addEventListener('click', () => {
                setCookie('cookie-consent', 'accepted', 365);
                consentPopup.style.display = 'none';
                console.log('User accepted all cookies.');
            });

            rejectCookiesButton.addEventListener('click', () => {
                setCookie('cookie-consent', 'rejected', 365);
                consentPopup.style.display = 'none';
                console.log('User rejected all cookies.');
            });
        });
    </script>
</body>
</html>
