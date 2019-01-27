<?php

namespace Uenoryo\Awsps\Exporter;

use Uenoryo\Awsps\Exporter;

class Json implements Exporter
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
