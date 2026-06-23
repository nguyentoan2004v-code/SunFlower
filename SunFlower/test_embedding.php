<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = config('services.gemini.key');
$url = "https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$apiKey}";

$response = \Illuminate\Support\Facades\Http::post($url, [
    "model" => "models/text-embedding-004",
    "content" => [
        "parts" => [
            ["text" => "Bó Hoa Hồng Sinh Nhật"]
        ]
    ]
]);

if ($response->successful()) {
    $data = $response->json();
    echo "Embedding dimension: " . count($data['embedding']['values']) . "\n";
    echo "Sample: " . $data['embedding']['values'][0] . "\n";
} else {
    echo "Error: " . $response->body();
}
