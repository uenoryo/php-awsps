<?php

namespace Uenoryo\Awsps\Expoter;

use Uenoryo\Awsps\Expoter;

class Plain implements Expoter
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
