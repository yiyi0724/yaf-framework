<?php


function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}


$controller = new \Mvc\Controller();

$goodsModel = $controller->getModel('goods');

$result = $goodsModel->where([['goods_id'=>1,'type'=>'5'], 'goods_name'=>'test'])->select();

echo '<pre>';
print_r($result);
exit;