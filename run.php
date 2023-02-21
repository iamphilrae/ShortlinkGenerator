<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/vendor/larapack/dd/src/helper.php';

$app = new ShortlinkGenerator\Bootstrap();
$results = $app->run();

print_r($results !== false ? $results : "\n[ERR] " . $app->ERR_RESPONSE . "\n\n");

