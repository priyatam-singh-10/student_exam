<?php

return [
    'documentations' => [
        'default' => [
            'api' => [
                'title' => env('APP_NAME', 'Laravel').' API',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'docs' => storage_path('api-docs'),
                'annotations' => [
                    base_path('app/Http/Controllers'),
                ],
            ],
        ],
    ],
];

