<?php
 
$ip = '192.168.66.66';
echo ip2long($ip),'<hr/>';
echo PHP_INT_SIZE;exit;

function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}

$lotoLogic = new \Logic\Loto();

//define('DEBUG_SQL', TRUE);

// 获取某个用户的股票投注信息
$lotoLogic->getMyBetInfo(['uid NL'=>'chen', 'type >'=>3, 'uid B'=>[1,5]]);

header('Content-Type:text/html;charset=UTF-8');
echo '<pre>';
print_r($myBets);