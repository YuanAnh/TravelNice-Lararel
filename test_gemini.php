<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $s = new App\Services\GeminiService();
    echo $s->chat([], 'xin chao');
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}