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
        	[
	        	'title' => 'success',
	        	'input' => function() {
	        		$cnf = Config::new();
	        		$cnf->region     = 'test-region';
	        		$cnf->path       = '/Test';
	        		$cnf->exportType = 'json';
	        		return $cnf;
	        	},
	        	'expect' => [
	        		'region' => 'test-region',
	        		'path'   => '/Test',
	        	],
        	],
        	[
	        	'title' => 'success default config',
	        	'input' => function() {
	        		return Config::new();
	        	},
	        	'expect' => [
	        		'region' => 'ap-northeast-1',
	        		'path'   => '/',
	        	],
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new($t['input']());
        	$this->assertSame($client->ssmClient->getRegion(), $t['expect']['region'], $t['title']);

        	$this->assertSame($client->path, $t['expect']['path'], $t['title']);

        	$this->assertNotNull($client->exporter, $t['title']);
        }
    }
}



