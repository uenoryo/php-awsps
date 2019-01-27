<?php

require_once '../src/Client.php';
require_once '../src/Config.php';
require_once '../src/Param.php';
require_once '../src/Expoter.php';
require_once '../src/Expoter/Json.php';

use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

$config = Config::new();
$client = Client::new($config);

$result = $client->fetch()->export();
print_r($result);
