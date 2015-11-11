<?php


// 协议
$build['scheme'] = "{$_SERVER['REQUEST_SCHEME']}://";
// 主机
$build['host'] = $_SERVER['HTTP_HOST'];
// 解析pathinfo和query_string
$info = parse_url($_SERVER['REQUEST_URI']);
// path信息
$build['path'] = isset($info['path']) ? $info['path'] : NULL;
// query信息
$build['query'] = NULL;
if(isset($info['query']))
{
	parse_str($info['query'], $query);
	$build['query'] = http_build_query($query);
	$build['query'] = $build['query'] ? $build['query'] . '&' : $build['query'];
}
// 完整路径
echo "{$build['scheme']}{$build['host']}{$build['path']}?{$build['query']}";

exit;

# 项目目录
define('APPLICATION_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
# 配置目录
define('CONF_PATH', APPLICATION_PATH.'conf'.DIRECTORY_SEPARATOR);

# 启动框架
$app = new \Yaf\Application(CONF_PATH.'application.ini');
$app->bootstrap();
$app->run();