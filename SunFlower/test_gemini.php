<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = config('services.gemini.key');

$models = [
    'gemini-2.5-flash',
    'gemini-flash-latest',
    'gemini-3.5-flash'
];

foreach ($models as $model) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    $response = \Illuminate\Support\Facades\Http::post($url, [
        'contents' => [['parts' => [['text' => 'Hi']]]]
    ]);
    echo "{$model}: " . $response->status() . " - " . substr($response->body(), 0, 150) . "\n\n";
}
