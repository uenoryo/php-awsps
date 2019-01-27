<?php

require_once '../vendor/autoload.php';

use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

$config = Config::new();
$config->path = '/Uenoryo/Development';
$client = Client::new($config);

$result = $client->fetch()->export();
print_r($result);
