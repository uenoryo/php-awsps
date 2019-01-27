<?php

namespace Uenoryo\Awsps\Exporter;

use Uenoryo\Awsps\Exporter;

class Plain implements Exporter
{
    public function export($data)
    {
        $result = '';
        foreach ($data as $r) {
            $row = $r->name.'='.$r->value.PHP_EOL;
            $result .= $row.PHP_EOL;
        }
        return $result;
    }
}
