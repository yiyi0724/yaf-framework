<?php

// 目录分隔符
define('DS', DIRECTORY_SEPARATOR);
// 公开项目
define('PUBLIC_PATH', sprintf("%s%s", __DIR__, DS));
// 站点目录
define('SITE_PATH', sprintf("%s%s", dirname(__DIR__), DS));
// 代码目录
define('APP_PATH', sprintf("%sapp%s",SITE_PATH, DS));
// 配置目录
define('CONF_PATH', sprintf("%sconf%s",SITE_PATH, DS));
// 数据目录
define('DATA_PATH', sprintf("%sdata%s",SITE_PATH, DS));

// 启动框架
$app = new \Yaf\Application(sprintf("%sapp.ini", CONF_PATH));
$app->bootstrap()->run();