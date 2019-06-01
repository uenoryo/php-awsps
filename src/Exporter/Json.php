<?php

namespace Uenoryo\Awsps\Exporter;

use Uenoryo\Awsps\Exporter;

class Json implements Exporter
{
	public $escapeSlush = false;

	public function export($data)
	{
		$result = [];
		foreach ($data as $r) {
			$result[$r->name] = $r->value;
		}

		$option = null;
		if (! $this->escapeSlush) {
			$option = JSON_UNESCAPED_SLASHES;
		}
		return json_encode($result, $option);
	}
}
