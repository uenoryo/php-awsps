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
        	$this->assertSame($client->ssmClient->getRegion(), $t['expect']['region'], $t['title']);

        	$this->assertSame($client->path, $t['expect']['path'], $t['title']);

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
        			$this->assertSame(Exception::class, $t['error']);
        		}
        		continue;
        	}

        	$client->setExporter($t['input']);
        	$this->assertSame(get_class($client->exporter), $t['expect'], $t['title']);
        }
    }

    public function testFetch()
    {
        $tests = [
        	[
	        	'title'  => 'success case',
	        	'expect' => [
	        		new Param,
	        		new Param,
	        		new Param,
	        	],
	        	'error'  => null,
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new(Config::new());
        	$client->ssmClient = new MockSsmClient;

        	$client->fetch();
        	$result = $client->params;

        	$this->assertSame(count($result), count($t['expect']), $t['title']);
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
					'Type'    => 'dummy type1',
					'Value'   => 'dummy value1',
					'Name'    => 'dummy Name1',
					'Version' => 'dummy version1',
				],
				[
					'Type'    => 'dummy type2',
					'Value'   => 'dummy value2',
					'Name'    => 'dummy Name2',
					'Version' => 'dummy version2',
				],
				[
					'Type'    => 'dummy type3',
					'Value'   => 'dummy value3',
					'Name'    => 'dummy Name3',
					'Version' => 'dummy version3',
				],
			],
		];
	}
}
