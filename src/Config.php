<?php

namespace Uenoryo\Awsps;

class Config
{
    /* @var region */
    public $region = 'ap-northeast-1';

    /* @var version */
    public $version = 'latest';

    /* @var path */
    public $path = '/';

    /* @var export type */
    public $exportType = '';

    /* @var escape slush when encode to json */
    public $escapeSlush = false;

    public static function new()
    {
        return new Self;
    }
}
