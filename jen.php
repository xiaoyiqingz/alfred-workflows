<?php
require __DIR__  . '/vendor/autoload.php';

use Alfred\Workflows\Workflow;

$query = "";
if (isset($argv[1])) {
  $query = $argv[1];
}

$w = new Workflow();
$url =  $w->env('url');
$name = $w->env('name');
$token = $w->env('token');

$access = sprintf("%s:%s", $name, $token);

$ch = curl_init();
$uri = sprintf("%s/search/suggest?query=%s", $url, $query);
curl_setopt($ch, CURLOPT_URL, $uri);
curl_setopt($ch, CURLOPT_USERPWD, $access);
curl_setopt($ch, CURLOPT_PORT, 443);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

$data = json_decode($response, true);

if (isset($data['suggestions'])) {
  foreach ($data['suggestions'] as $d) {
    $name = $d['name'];
    $w->item()
      ->title($name)
      ->arg($name);
  }
}
$w->output();
