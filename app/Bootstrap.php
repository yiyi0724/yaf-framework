<?php

/**
 * 初始化自定义框架
 * @author enyccc
 * @version 1.0
 */
use \Yaf\Session;
use \Yaf\Registry;
use \Traits\Route;
use \Yaf\Dispatcher;
use \Yaf\Config\Ini;
use \Yaf\Application;
use \Traits\Response;
use \Driver\PDO as PDOLib;
use \Yaf\Bootstrap_Abstract;
use \Driver\Redis as RedisLib;

class Bootstrap extends Bootstrap_Abstract {

    /**
     * 修改路由信息
     * @param \Yaf\Dispatcher $dispatcher 分发对象
     * @return void
     */
    public function _initRoute(Dispatcher $dispatcher) {
        // 路由对象
        $router = $dispatcher->getRouter();
        // 自定义路由协议
        $router->addRoute('enyRouter', new Route());
        // 路由重写正则
        $router->addConfig(new Ini(sprintf("%sroute.ini")));
    }

    /**
     * 注册插件
     * @param \Yaf\Dispatcher $dispatcher 分发对象
     * @return void
     */
    public function _initPlugin(Dispatcher $dispatcher) {
        $dispatcher->registerPlugin(new HandlerPlugin());
    }

    /**
     * 自定义模板对象
     * @param \Yaf\Dispatcher $dispatcher 分发对象
     * @return void
     */
    public function _initTemplate(Dispatcher $dispatcher) {
        $dispatcher->setView(new Response());
    }

    /**
     * 修改php.ini的默认配置
     * @param Yaf\Dispatcher $dispatcher 分发对象
     * @return void
     */
    public function _initRuntime(Dispatcher $dispatcher) {
        if ($runtime = Application::app()->getConfig()->get('runtime')) {
            foreach ($runtime->toArray() as $prefix => $suffix) {
                if (is_array($suffix)) {
                    foreach ($suffix as $option => $value) {
                        ini_set(sprintf("%s.%s", $prefix, $option), $value);
                    }
                } else {
                    ini_set($prefix, $suffix);
                }
            }
        }
    }

    /**
     * 注册驱动
     * @param Yaf\Dispatcher $dispatcher 分发对象
     * @return void
     */
    public function _initDriver(Dispatcher $dispatcher) {
        if ($drivers = new Ini(sprintf("%sdriver.ini", CONF_PATH))) {
            // 注册数据库
            foreach ($drivers->get('database') as $name => $driver) {
                $database = PDOLib::getInstance($driver->type, $driver->host, $driver->port,
                    $driver->dbname, $driver->charset, $driver->username, $driver->password);
                \Yaf\ENVIRON != 'product' and $database->setDebug();
                Registry::set("database.{$name}", $database);
            }

            // 注册redis
            foreach ($drivers->get('redis') as $name => $driver) {
                // 创建redis对象
                $redis = new \Redis();
                // 持久性连接
                $redis->pconnect($driver->host, $driver->port, (float)$driver->timeout);
                // 选项设置
                foreach ($driver->options as $key => $option) {
                    $redis->setOption(constant(sprintf("\\Redis::OPT_%s", strtoupper($key))), $option);
                }
                // 密码验证
                $driver->auth and $redis->auth($driver->auth);
                // 全局保存
                Registry::set("redis.{$name}", $redis);
            }
        }
    }
}