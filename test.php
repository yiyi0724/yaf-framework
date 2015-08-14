<?php

header('Content-Type:text/html;charset=utf-8');
echo date('m月d日');
exit;

function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}


$lotoLogic = new \Logic\Loto();

// 获取某个用户的股票投注信息
$lotoLogic->getMyBetInfo(array('uid'=>65803, 'type'=>3));

header('Content-Type:text/html;charset=UTF-8');
echo '<pre>';
print_r($myBets);