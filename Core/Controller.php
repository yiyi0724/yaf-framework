<?php
namespace Core;

class Controller
{
    public function __construct() {
        $this->initialize();
    }
    /**
     * 初始化
     */
    protected function initialize()
    {
        // 定义UID
        define('UID', FALSE);
        define('IP', FALSE);
    }
    
    /**
     * json输出
     */
    protected function json()
    {
        
    }
    
    /**
     * 视图输出
     */
    protected function view()
    {
        
    }
}