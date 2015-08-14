<?php


function __autoload($class)
{
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require(__DIR__."/$class.php");
}


$controller = new \Mvc\Controller();

$model = $controller->getModel();

//define('DEBUG_SQL', TRUE);

$result = $model->from('goods')->select()->fetchAll();
header('Content-Type:text/html;charset=UTF-8');
echo '<pre>';
print_r($result);
