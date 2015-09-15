<?php

function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}

$lotoLogic = new \Logic\Mission();

define('DEBUG_SQL', TRUE);

// 获取某个用户的股票投注信息
$myBets = $lotoLogic->getMyMission(['uid '=>65803, 'type'=>[1,2,3,4,5]]);

header('Content-Type:text/html;charset=UTF-8');
echo '<pre>';
print_r($myBets);