<?php
require __DIR__ . '/vendor/autoload.php';

$openapi = \OpenAPI\Scan([
    'app/Http/Controllers'
], [
    'pattern' => '*.php'
]);

$spec = [
    'openapi' => '3.0.0',
    'info' => [
        'title' => 'Fenix Educação API',
        'version' => '1.0.0'
    ],
    'servers' => [
        ['url' => 'http://localhost:8080/api/v1']
    ],
    'paths' => $openapi->paths ?? [],
    'components' => [
        'schemas' => $openapi->components['schemas'] ?? []
    ]
];

file_put_contents('storage/api-docs/api-docs.json', json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Docs generated successfully\n";
