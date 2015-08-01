<?php

function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}



$controller = new \Mvc\Controller();

$goodsModel = $controller->getModel('goods');

echo $goodsModel->insert(array(array('goods_name'=>'chenlu'), array('goods_name'=>'chenxiaobo')));
/* echo '<pre>';
print_r(
    $goodsModel->field('goods_id, goods_name')
    ->where(
        [
        'goods_id b'=>[1,2],
        'goods_name'=>'chenlu'
        ]
    )
    ->order('goods_id DESC')
    ->limit(0,2)
    ->select()
    ); */

//echo $goodsModel->where(['goods_id'=>3])->limit(1)->delete();

//echo $goodsModel->replace(['goods_id'=>8,'goods_name'=>'chenxiaobo', 'type'=>2]);