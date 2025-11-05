<?php
return [
    "pages" => [
        "MAINTENANCE" => "coming-soon",
    ],
    "session" =>[
        'lifetime' => getenv('SESSION_LIFETIME' ) ?? 3600, // 1 h.
        'auth-lifetime' => getenv('SESSION_LIFETIME') ?? 3600 , // 1 h.
        'timeout' => getenv('TIMEOUT_SESSION') ?? 900 // 15 mins
    ]
];
