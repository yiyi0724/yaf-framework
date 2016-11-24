<?php

/**
 * 以/模块/控制器/方法的路由调用方式
 * @author enyccc
 * @version 1.0.0
 */
namespace Traits;

use \Yaf\Application;
use \Yaf\Route_Interface;

class Route implements Route_Interface {

    /**
     * 默认路由信息
     * @var array
     */
    protected $route = array(
        'module' => 'www',
        'controller' => 'index',
        'action' => 'index'
    );

    /**
     * 已在Yaf中注册的模块
     * @var array
     */
    protected $modules = array();

    /**
     * 构造函数，获取所有模块信息并删除默认index模块
     */
    public function __construct() {
        $this->modules = Application::app()->getModules();
        unset($this->modules[array_search('Index', $this->modules)]);
        foreach ($this->modules as $key => $module) {
            $this->modules[$key] = strtolower($module);
        }
    }

    /**
     * 路由调度
     * @param \Yaf\Request_Abstract $request http请求对象
     * @return boolean TRUE表示和其他路由协议共存
     */
    public function route($request) {
        // 解析url信息
        $uri = explode('/', trim($request->getRequestUri(), '/'));
        $module = strtolower($uri[0]);

        // 分析url信息
        if (in_array($module, $this->modules)) {
            $this->route['module'] = $module;
            array_splice($uri, 0, 1);
        }
        if (isset($uri[0])) {
            $this->route['controller'] = $uri[0];
        }
        if (isset($uri[1])) {
            $this->route['action'] = $uri[1];
        }

        // 修改调度信息
        $request->setModuleName($this->route['module']);
        $request->setControllerName($this->route['controller']);
        $request->setActionName($this->route['action']);

        return TRUE;
    }

    /**
     * 不知道什么鬼东西，但是又必须继承
     * @param array $info 不知道什么鬼还不调用的内容
     * @param array $query 不知道什么鬼还不调用的内容
     * @return void
     */
    public function assemble(array $info, array $query = NULL) {
    }
}
