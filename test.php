<?php


function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}


$controller = new \Mvc\Controller();

$goodsModel = $controller->getModel('goods');
//echo $goodsModel->insert(array(array('goods_name'=>'linxiaohua','type'=>24), array('goods_name'=>'chenxiaobo', 'type'=>26)), \Driver\Sql::RESULT_ROW);
//echo '<pre>';print_r( $goodsModel->where(['goods_id >'=>1])->select() );

echo $goodsModel->where(['goods_id'=>4])->delete();