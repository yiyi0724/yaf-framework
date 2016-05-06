<?php

// 站点目录
define('SITE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 公开项目
define('PUBLIC_PATH', __DIR__ . DIRECTORY_SEPARATOR);
// 代码目录
define('APPLICATION_PATH', 'app' . DIRECTORY_SEPARATOR);
// 配置目录
define('CONF_PATH', SITE_PATH . 'conf' . DIRECTORY_SEPARATOR);
// 数据目录
define('DATA_PATH', SITE_PATH . 'data' . DIRECTORY_SEPARATOR);

// 启动框架
$app = new \Yaf\Application(CONF_PATH . 'app.ini');
$app->bootstrap()->run();