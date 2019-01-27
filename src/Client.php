<?php

namespace Uenoryo\Awsps;

require '../vendor/autoload.php';

use Aws\Ssm\SsmClient;
use Exception;

class Client
{
    /* @var ssmClient */
    public $ssmClient;

    /* @var 取得先のパス */
    public $path;

    /* @var パラメータをキャッシュする用 */
    public $paramArray = [];

    /**
     * $config を読み込み、 Client を初期化して返す.
     *
     * @param Uenoryo\Awsps\Config
     *
     * @return Uenoryo\Awsps\Client
     */
    public static function new(Config $config)
    {
        $client = new Self;
        $client->ssmClient = new SsmClient([
            'version' => $config->version,
            'region'  => $config->region,
        ]);
        $client->path = $config->path;
        $client->validateSelf();
        return $client;
    }

    /**
     * 自身に設定されている値が有効かどうかをチェックする.
     *
     * @return void
     */
    public function validateSelf()
    {
        if (substr($this->path, 0, 1) !== '/') {
            throw new Exception(sprintf('Invalid fetch path "%s".', $this->path));
        }
    }

    /**
     * パラメータを取得し、paramArray にセットする.
     *
     * @return Uenoryo\Awsps\Client
     */
    public function fetch()
    {
        try {
            $response = $this->ssmClient->getParametersByPath([
                'Path'      => $this->path,
                'Recursive' => true,
            ]);
            $this->paramArray = $response->toArray()['Parameters'];
        } catch (Exception $e) {
            throw new Exception(sprintf('Fetch parameter failed, error: %s', $e->getMessage()));
        }
        return $this;
    }

    protected function newParamsFromArray($paramArray)
    {
        $res = [];
        foreach ($paramArray as $data) {
            $param = new Param;
        }
    }
}
