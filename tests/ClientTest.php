<?php

namespace Uenoryo\Awsps\Test;

use PHPUnit\Framework\TestCase;
use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

class ClientTest extends TestCase
{
    public function testNew()
    {
        $tests = [
        	'title' => 'success',
        	'input' => function() {
        		$cnf = Config::new();
        		$cnf->region     = 'test-region';
        		$cnf->version    = '1.0.0';
        		$cnf->path       = '/Test';
        		$cnf->exportType = 'json';
        		return $cnf;
        	},
        	'expect' => [
        		'region'     => 'test-region',
        		'version'    => '1.0.0',
        		'path'       => '/Test',
        		'exportType' => 'json',
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new($t['input']());
        	$this->assetSame($client->path, $t['expect']['path'], $t['title']);
        }
    }
}



