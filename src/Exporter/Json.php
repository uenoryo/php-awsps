<?php

namespace Uenoryo\Awsps\Exporter;

use Uenoryo\Awsps\Exporter;

class Json implements Exporter
{
	public $noEscape = false;

	public function export($data)
	{
		$result = [];
		foreach ($data as $r) {
			$result[$r->name] = $r->value;
		}

		$options = [];
		if ($this->noEscape) {
			$options = [
				JSON_UNESCAPED_SLASHES,
				JSON_UNESCAPED_UNICODE,
			];
		}
		return json_encode($result, $options);
	}
}
