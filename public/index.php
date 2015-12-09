<?php

# 项目目录
define('APPLICATION_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
# 配置目录
define('CONF_PATH', APPLICATION_PATH.'conf'.DIRECTORY_SEPARATOR);
# 启动框架
$app = new \Yaf\Application(CONF_PATH.'application.ini');
$app->bootstrap()->run();