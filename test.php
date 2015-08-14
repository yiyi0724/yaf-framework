<?php


function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}


$controller = new \Mvc\Controller();

$my5755 = $controller->getMy5755Db();
$platform = $controller->getPlatformDb();

//define('DEBUG_SQL', TRUE); // 输出调试的sql语句,不真正的执行

$myBets = $my5755->table('loto_userbets')->where(['uid'=>65803])->select()->fetchAll();

$bidList = $controller->getFileds($myBets, 'bid');

$bets = $platform->field('bet')->table('loto_bets')->where(['id'=>$bidList])->select()->fetchAll();



header('Content-Type:text/html;charset=UTF-8');
echo '<pre>';
print_r($bets);
