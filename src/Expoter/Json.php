<?php

namespace Uenoryo\Awsps\Expoter;

use Uenoryo\Awsps\Expoter;

class Json implements Expoter
{
	public function export($data)
	{
		return json_encode($data);
	}
}
