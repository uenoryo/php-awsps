<?php

namespace Uenoryo\Awsps\Expoter;

use Uenoryo\Awsps\Expoter;

class Json implements Expoter
{
	public function export($data)
	{
		$result = [];
		foreach ($data as $r) {
			$result[$r->name] = $r->value;
		}
		return json_encode($result);
	}
}
