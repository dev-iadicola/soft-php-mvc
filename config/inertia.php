<?php

declare(strict_types=1);

return [
    'version' => '1',
    'root_element_id' => 'app',
    'entrypoint' => 'frontend/app.tsx',
    'manifest_path' => basepath('assets/build/.vite/manifest.json'),
    'asset_public_path' => '/assets/build',
];
