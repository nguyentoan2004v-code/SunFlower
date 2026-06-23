<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKey = config('services.gemini.key');
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

$response = \Illuminate\Support\Facades\Http::post($url, [
    'contents' => [['parts' => [['text' => 'Hello']]]]
]);

echo $response->status() . "\n";
echo $response->body();
