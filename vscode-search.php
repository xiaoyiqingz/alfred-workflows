<?php
require __DIR__  . '/vendor/autoload.php';

use Alfred\Workflows\Workflow;
use Alfred\Workflows\ItemParam\Type;

$query = "";
if (isset($argv[1])) {
    $query = $argv[1];
}

$w = new Workflow();

$home = exec('printf "$HOME"');

$manifest = file_get_contents("{$home}/Library/Application Support/Code/User/globalStorage/storage.json");
$data = json_decode($manifest, true)['lastKnownMenubarData']['menus']['File']['items'];

$payload = [];
foreach ($data as $item) {
    if ($item['id'] == 'submenuitem.MenubarRecentMenu') {
        foreach ($item['submenu']['items'] as $subItem) {
            if (in_array($subItem['id'], ['openRecentFolder', 'openRecentFile'])) {
                $payload[] = [
                    'label' => $subItem['label'],
                    'path' => $subItem['uri']['path'],
                    'type' => $subItem['id'] == 'openRecentFolder' ? 'folder' : 'file',
                ];
            }
        }
    }
}

//print_r($payload);

if (!empty($payload)) {
    foreach ($payload as $p) {
        if (strpos($p['label'], $query)) {
            $w->item()
              ->title($p['label'])
              ->subtitle($p['path'])
              ->type(Type::TYPE_FILE)
              ->autocomplete($p['path'])
              ->arg($p['path']);
        }
    }
}

if (empty($w->items()->all())) {
    $w->item()
      ->title($query)
      ->subtitle('try to search in vscode ....')
      ->arg($query);
}

$w->output();
