<?php
require __DIR__  . '/vendor/autoload.php';

use Alfred\Workflows\Workflow;

$query = "";
if (isset($argv[1])) {
    $query = $argv[1];
}

$w = new Workflow();
$url =  $w->env('url');
$token = $w->env('token');
$sMode = $w->env('secure');

$ch = curl_init();
$uri = sprintf("%s/search/simple/?query=%s&contextLength=100", $url, $query);
curl_setopt($ch, CURLOPT_URL, $uri);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$headers = [
    'accept: application/json',
    'Authorization: Bearer ' . $token,
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if ($sMode == 'true') {
    curl_setopt($ch, CURLOPT_PORT, 443);
}

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);

//print_r($data);

if (empty($data)) {
    $w->item()
        ->title($query)
        ->subtitle("try to search in obsidian")
        ->arg($query);

    $w->output();

    return;
}

foreach ($data as $file) {
    $fileName = $file['filename'];
    $matches = $file['matches'] ?? [];

    foreach ($matches as $content) {
        if (isset($content['context'])) {
            $w->item()
                ->title($fileName)
                ->subtitle($content['context'])
                ->largeType($content['context'])
                ->arg(sprintf("obsidian://advanced-uri?vault=Obsidian Vault&filename=%s&line=%d", $fileName, $content['match']['start']));
        }
    }
}
$w->output();
