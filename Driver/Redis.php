<?php

namespace Driver;

class Redis
{
    /**
     * 禁止创建对象
     */
    private final function __construct(){}
    /**
     * 禁止创建对象
     */
    private final function __clone(){}

    /**
     * 静态调用方式
     * @param string 方法名
     * @param array 参数
     */
    public static function __callStatic($method, $args)
    {
        // 检查对象是否创建
        self::$instance OR self::create();
        // 执行回调函数
        return call_user_func_array(array($object, $method), $args);
    }
    
    /**
     * 如果redis没有创建则进行创建
     * @throws \RedisException
     */
    protected static function create()
    {
        foreach(C('redis') as $key=>$node)
        {
            $redis = new \Redis();
            // redis连接
            if($redis->connect($node['host'], $node['port'], $node['timeout']))
            {
                // 是否需要验证
                !$node['password'] OR $redis->auth($node['password']);
                // 增加节点
                Consistent::addNode("redis{$key}", $redis);
                // 删除临时对象
                unset($redis);
            }
            else
            {
                throw new \RedisException("Redis Connection Error: {$node['ip']}");
            }
        }
        self::$instance = TRUE;
    }
}