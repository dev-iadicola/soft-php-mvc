<?php

// Dischi storage del framework.
// - 'public': file accessibili dal browser (immagini, media). Serviti via symlink o alias su /storage/
// - 'private': file riservati all'admin (backup, export, file interni). Non esposti al web.
return [
    'public_base' => '/storage',
    'disks' => [
        'public' => [
            'driver'     => 'local',
            'root'       => storagePath('/app/public'),
            'visibility' => 'public',
        ],

        'private' => [
            'driver'     => 'local',
            'root'       => storagePath('/app/private'),
            'visibility' => 'private',
        ],
    ],
];
