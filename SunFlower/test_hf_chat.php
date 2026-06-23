<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$token = config('services.huggingface.token');
$url = 'https://router.huggingface.co/hf-inference/v1/chat/completions';

$models = [
    'mistralai/Mixtral-8x7B-Instruct-v0.1',
    'mistralai/Mistral-7B-Instruct-v0.2',
    'HuggingFaceH4/zephyr-7b-beta',
    'TinyLlama/TinyLlama-1.1B-Chat-v1.0',
    'google/flan-t5-xxl',
    'OpenAssistant/oasst-sft-4-pythia-12b-epoch-3.5',
    'NousResearch/Nous-Hermes-2-Mixtral-8x7B-DPO'
];

foreach ($models as $model) {
    $response = Illuminate\Support\Facades\Http::withToken($token)->timeout(15)->withHeaders(['Content-Type'=>'application/json'])->post($url, [
        'model' => $model,
        'messages' => [['role'=>'user', 'content'=>'Hi']],
        'max_tokens' => 10
    ]);
    echo "{$model}: " . $response->status() . " - " . substr($response->body(), 0, 100) . "\n";
}
