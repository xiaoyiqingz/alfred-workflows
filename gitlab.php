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

$ch = curl_init();
$uri = sprintf("%s/api/v4/search?search=%s&scope=projects&private_token=%s", $url, $query, $token);
curl_setopt($ch, CURLOPT_URL, $uri);
curl_setopt($ch, CURLOPT_PORT, 443);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);

if (!empty($data)) {
    foreach ($data as $d) {
        $name = $d['name'];
        $w->item()
          ->title($name)
          ->subtitle($d['path_with_namespace'])
          ->arg($name);
    }
} else {
    $w->item()
      ->title($query)
      ->subtitle('try to search in gitlab ....')
      ->arg($query);
}
$w->output();
