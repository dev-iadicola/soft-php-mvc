<?php
return [
    'public_base' => '/storage/app/public',
    'disks' => [
        'public' => [
            'drive'      => 'local',
            'root'       => storagePath('/app/public'),
            'visibility' => 'public',
        ],

        'private'=> [
            'drive' => 'locale',
            'root' => storagePath('/app/private'),
            'visibility' => 'private',
        ]
    ]
];
