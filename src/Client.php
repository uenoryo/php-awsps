<?php

namespace Uenoryo\Awsps;

use Uenoryo\Awsps\Exporter\Plain;
use Uenoryo\Awsps\Exporter\Json;
use Aws\Ssm\SsmClient;
use Exception;

class Client
{
    /* SSMへの最大リクエスト回数 */
    const MAX_REQUEST_COUNT = 20;

    /* @var ssmClient */
    public $ssmClient;

    /* @var 取得先のパス */
    public $path;

    /* @var パラメータ */
    public $params = [];

    /* @var エクスポータ */
    public $exporter;

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
        $client->setExporter($config->exportType, $config->escapeSlush);
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
     * パラメータを取得し、params にセットする.
     * NOTE: 1リクエストで最大10件しか取得できない仕様なので MAX_REQUEST_COUNT 回まで連続でリクエストして取得する
     *
     * @return Uenoryo\Awsps\Client
     */
    public function fetch()
    {
        try {
            // ページネーション用のトークン
            $nextToken = null;

            for ($i = 0; $i < self::MAX_REQUEST_COUNT; $i++) {
                $response = $this->ssmClient->getParametersByPath([
                    'Path'           => $this->path,
                    'WithDecryption' => true,
                    'Recursive'      => true,
                    'NextToken'      => $nextToken,
                ]);
                $body = $response->toArray();
                $paramsArray = $body['Parameters'];
                array_push($this->params, ...$this->newParamsFromArray($paramsArray));

                if (! isset($body['NextToken'])) {
                    break;
                }

                $nextToken = $body['NextToken'];
                sleep(0.5);
            }
        } catch (Exception $e) {
            throw new Exception(sprintf('Fetch parameter failed, error: %s', $e->getMessage()));
        }
        return $this;
    }

    /**
     * $param を $exporterを使用して変換し、返す.
     *
     * @return mixed
     */
    public function export()
    {
        return $this->exporter->export($this->params);
    }

    /**
     * $type の $exporter をセットする.
     *
     * @param $type string
     *
     * @return Uenoryo\Awsps\Client
     */
    public function setExporter(string $type, ?bool $escapeSlush = false)
    {
        switch (true) {
            case $type === '' || $type === 'plain' || $type === 'Plain':
                $this->exporter = new Plain;
                break;
            case $type === 'json' || $type === 'Json':
                $this->exporter = new Json;
                if ($escapeSlush) {
                    $this->exporter->escapeSlush = true;
                }
                break;
            default:
                throw new Exception("Invalid export type:'{$type}'");
        }
        return $this;
    }

    /**
     * AWSからのレスポンスから$paramsを初期化して返す.
     *
     * @return array
     */
    protected function newParamsFromArray($paramArray)
    {
        $res = [];
        foreach ($paramArray as $data) {
            $param = new Param;
            $name = $data['Name'] ?? '';

            // $name がパスである場合、末尾の名前だけを取得する
            if (strpos($name, '/') !== false) {
                $name = ltrim(strrchr($name, '/'), '/');
            }

            $param->name             = $name;
            $param->type             = $data['Type'] ?? '';
            $param->value            = $data['Value'] ?? '';
            $param->version          = $data['Version'] ?? '';
            $param->lastModifiedDate = $data['LastModifiedDate'] ?? '';
            $param->arn              = $data['ARN'] ?? '';
            $res[] = $param;
        }
        return $res;
    }
}
