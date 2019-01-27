<?php

namespace Uenoryo\Awsps\Test;

use PHPUnit\Framework\TestCase;
use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;
use Exception;

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

    public function testValidateSelf()
    {
        $tests = [
        	[
	        	'title' => 'success',
	        	'init'  => function() {
	        		$cnf = Config::new();
	        		$client = Client::new($cnf);
	        		return $client;
	        	},
	        	'error' => null,
        	],
        	[
	        	'title' => 'error: invalid path',
	        	'init'  => function() {
	        		$cnf = Config::new();
	        		$cnf->path = 'InvalidPath';
	        		$client = Client::new($cnf);
	        		return $client;
	        	},
	        	'error' => Exception::class,
        	],
        ];

        foreach ($tests as $t) {
        	if ($t['error'] !== null) {
        		try {
        			$client = $t['init']();
        			$client->validateSelf();
        		} catch (Exception $e) {
        			$this->assertSame(Exception::class, $t['error']);
        		}
        		continue;
        	}

      		$client = $t['init']();
      		$this->assertNull($client->validateSelf(), $t['title']);
        }
    }

    public function testSetExporter()
    {
        $tests = [
        	[
	        	'title'  => 'success json',
	        	'input'  => 'Json',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Json',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'success plain',
	        	'input'  => 'Plain',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Plain',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'success default',
	        	'input'  => '',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Plain',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'error invalid exporter type',
	        	'input'  => 'invalid type',
	        	'expect' => null,
	        	'error'  => Exception::class,
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new(Config::new());

        	if ($t['error'] !== null) {
        		try {
        			$client->setExporter($t['input']);
        		} catch (Exception $e) {
        			$this->assertSame(Exception::class, $t['error']);
        		}
        		continue;
        	}

        	$client->setExporter($t['input']);
        	$this->assertSame(get_class($client->exporter), $t['expect'], $t['title']);
        }
    }
}
