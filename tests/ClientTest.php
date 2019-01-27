<?php

namespace Uenoryo\Awsps\Test;

use PHPUnit\Framework\TestCase;
use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;
use Uenoryo\Awsps\Param;
use Exception;

class ClientTest extends TestCase
{
    public function testNew()
    {
        $tests = [
        	[
	        	'title' => 'success case',
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
	        	'title' => 'success case, default config',
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
        	$this->assertSame($t['expect']['region'], $client->ssmClient->getRegion(), $t['title']);

        	$this->assertSame($t['expect']['path'], $client->path, $t['title']);

        	$this->assertNotNull($client->exporter, $t['title']);
        }
    }

    public function testValidateSelf()
    {
        $tests = [
        	[
	        	'title' => 'success case',
	        	'init'  => function() {
	        		$cnf = Config::new();
	        		$client = Client::new($cnf);
	        		return $client;
	        	},
	        	'error' => null,
        	],
        	[
	        	'title' => 'error case, invalid path',
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
        			$this->assertSame($t['error'], get_class($e));
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
	        	'title'  => 'success case: json',
	        	'input'  => 'Json',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Json',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'success case: plain',
	        	'input'  => 'Plain',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Plain',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'success case :default',
	        	'input'  => '',
	        	'expect' => 'Uenoryo\Awsps\Exporter\Plain',
	        	'error'  => null,
        	],
        	[
	        	'title'  => 'error case, invalid exporter type',
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
        			$this->assertSame($t['error'], get_class($e));
        		}
        		continue;
        	}

        	$client->setExporter($t['input']);
        	$this->assertSame($t['expect'], get_class($client->exporter), $t['title']);
        }
    }

    public function testFetch()
    {
        $tests = [
        	[
	        	'title'  => 'success case',
	        	'expect' => [
	        		[
	        			'name'    => 'DUMMY1',
	        			'value'   => 'dummy value 1',
	        			'type'    => 'dummy type 1',
	        			'version' => 'dummy version 1',
	        		],
	        		[
	        			'name'    => 'DUMMY2',
	        			'value'   => 'dummy value 2',
	        			'type'    => 'dummy type 2',
	        			'version' => 'dummy version 2',
	        		],
	        		[
	        			'name'    => 'DUMMY3',
	        			'value'   => 'dummy value 3',
	        			'type'    => 'dummy type 3',
	        			'version' => 'dummy version 3',
	        		],
	        	],
	        	'error'  => null,
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new(Config::new());
        	$client->ssmClient = new MockSsmClient;

        	$client->fetch();
        	$result = $client->params;

        	$this->assertSame(count($t['expect']), count($result), $t['title']);

        	foreach ($result as $i => $res) {
        		$this->assertSame($t['expect'][$i]['name'], $res->name, $t['title']);
        		$this->assertSame($t['expect'][$i]['value'], $res->value, $t['title']);
        		$this->assertSame($t['expect'][$i]['type'], $res->type, $t['title']);
        		$this->assertSame($t['expect'][$i]['version'], $res->version, $t['title']);
        	}
        }
    }
}

class MockSsmClient
{
	public function getParametersByPath()
	{
		return new MockResponse;
	}
}

class MockResponse
{
	public function toArray()
	{
		return [
			'Parameters' => [
				[
					'Type'    => 'dummy type 1',
					'Value'   => 'dummy value 1',
					'Name'    => 'DUMMY1',
					'Version' => 'dummy version 1',
				],
				[
					'Type'    => 'dummy type 2',
					'Value'   => 'dummy value 2',
					'Name'    => '/DUMMY2',
					'Version' => 'dummy version 2',
				],
				[
					'Type'    => 'dummy type 3',
					'Value'   => 'dummy value 3',
					'Name'    => '/DUMMY/DUMMY3',
					'Version' => 'dummy version 3',
				],
			],
		];
	}
}
