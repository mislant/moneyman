<?php

use Moneyman\App\Bootstrap;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$bootstrap = new Bootstrap();
$bootstrap->env();
(new yii\web\Application($bootstrap->configs()))->run();
