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

    public static function new()
    {
        return new Self;
    }
}
