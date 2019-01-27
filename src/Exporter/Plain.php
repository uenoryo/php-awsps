<?php

namespace Uenoryo\Awsps\Exporter;

use Uenoryo\Awsps\Exporter;

class Plain implements Exporter
{
	public function export($data)
	{
		$result = '';
		foreach ($data as $r) {
			$result .= $r->name . '=' . $r->value . PHP_EOL;
		}
		return $result;
	}
}
