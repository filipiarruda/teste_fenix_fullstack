<?php

return [
    "default" => "default",
    "documentations" => [
        "default" => [
            "api" => [
                "title" => "Fenix Educação API",
            ],
            "routes" => [
                "api" => "api/documentation",
                "docs" => "docs",
                "oauth2_callback" => "api/oauth2-callback",
            ],
            "paths" => [
                "docs_json" => "storage/api-docs/api-docs.json",
                "docs_yaml" => "storage/api-docs/api-docs.yaml",
                "format_to_use_for_docs" => "json",
                "docs_url" => "/docs",
                "annotations" => [
                    base_path("storage/api-docs"),
                    base_path("app/Http/Controllers"),
                ]
            ]
        ]
    ]
];
