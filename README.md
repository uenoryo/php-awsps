# AWS Parameter Store Client For PHP

AWSのParameter Storeから値を取ってくるマン

## 使い方

#### [0]

[AWSのパラメータストア](https://docs.aws.amazon.com/ja_jp/systems-manager/latest/userguide/systems-manager-paramstore.html)に値を入れておきます

E.g.

```sh
$ aws ssm put-parameter --name /Test/Env/HOSTNAME --value 172.17.0.1 --type String
$ aws ssm put-parameter --name /Test/Env/USERNAME --value root --type String
$ aws ssm put-parameter --name /Test/Env/PASSWORD --value 12ab34CD --type SecureString
```

#### [1]

composer を使ってインストールします

```sh
$ composer require uenoryo/php-awsps
```

#### [2]

値を取得します

E.g.

```php
<?php

require_once '../vendor/autoload.php';

use Uenoryo\Awsps\Config;
use Uenoryo\Awsps\Client;

$config = Config::new();
$config->path = '/Test/Env';
// $config->exportType = 'json';

$client = Client::new($config);
$result = $client->fetch()->export();
print_r($result);

/* [出力]
*
* HOMENAME=172.0.0.1
*
* USERNAME=root
*
* PASSWORD=12ab34CD
*/
```

#### [3]

.env に出力したりします (gitに入れたくない値などが共有できて便利)
