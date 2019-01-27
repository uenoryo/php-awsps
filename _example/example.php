<?php

require_once '../src/Client.php';
require_once '../src/Config.php';

use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

$config = Config::new();
$client = Client::new($config);

$client->fetch();
