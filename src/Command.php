<?php

namespace Uenoryo\Awsps;

use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

class Command
{
    public static function exec($args)
    {
        if (count($args) < 2 || $args[1] === '--help') {
            self::help();
            return;
        }

        if ($args[1] !== '--path') {
            echo 'Error: path is required.'.PHP_EOL;
            die();
        }

        if (!isset($args[2])) {
            echo 'Error: invalid path.'.PHP_EOL;
            die();
        }

        $config = Config::new();
        $config->path = $args[2];
        if (isset($args[3])) {
            if ($args[3] !== '--json') {
                echo "Error: invalid option. run [awsps --help] for more information.".PHP_EOL;
                die();
            }
            $config->exportType = 'json';

            if (isset($args[4])) {
                if ($args[4] !== '--escape-slush') {
                    echo "Error: invalid option. run [awsps --help] for more information.".PHP_EOL;
                    die();
                }
                $config->escapeSlush = true;
            }
        }

        $client = Client::new($config);
        print($client->fetch()->export());
        echo PHP_EOL;
        return;
    }

    public static function help()
    {
        echo 'Usage: awsps [options...]'.PHP_EOL.PHP_EOL;
        echo '--help            Show this help'.PHP_EOL;
        echo '--json            Export JSON format'.PHP_EOL;
        echo '--escape-slush    Escape slush when export JSON format'.PHP_EOL;
        echo '--path <path>     Target path of AWS Parameter store'.PHP_EOL.PHP_EOL;
    }
}
