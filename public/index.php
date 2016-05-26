<?php

echo '<pre>';
print_r($_SERVER);
EXIT;

// 目录分隔符
define('DS', DIRECTORY_SEPARATOR);
// 公开项目
define('PUBLIC_PATH', __DIR__ . DS);
// 站点目录
define('SITE_PATH', dirname(__DIR__) . DS);
// 代码目录
define('APP_PATH', SITE_PATH . 'app' . DS);
// 配置目录
define('CONF_PATH', SITE_PATH . 'conf' . DS);
// 数据目录
define('DATA_PATH', SITE_PATH . 'data' . DS);

// 启动框架
$app = new \Yaf\Application(CONF_PATH . 'app.ini');
$app->bootstrap()->run();