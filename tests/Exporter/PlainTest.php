<?php

namespace Uenoryo\Awsps\Exporter\Test;

use PHPUnit\Framework\TestCase;
use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;
use Uenoryo\Awsps\Param;

class PlainTest extends TestCase
{
    public function testExport()
    {
        $tests = [
        	[
	        	'title' => 'success case',
	        	'init' => function() {
	        		$param1 = new Param();
	        		$param1->name  = 'Dummy1';
	        		$param1->value = 'dummy1';

	        		$param2 = new Param();
	        		$param2->name  = 'Dummy2';
	        		$param2->value = 'dummy2';

	        		$param3 = new Param();
	        		$param3->name  = 'Dummy3';
	        		$param3->value = 'dummy3';

	        		return [$param1, $param2, $param3];
	        	},
	        	'expect' => "Dummy1=dummy1\nDummy2=dummy2\nDummy3=dummy3\n",
        	],
        ];

        foreach ($tests as $t) {
        	$client = Client::new(Config::new());
        	$client->setExporter('plain');
        	$client->params = $t['init']();

        	$this->assertSame($t['expect'], $client->export(), $t['title']);
        }
    }
}
