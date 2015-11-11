<?php
/**
 * 驱动基类
 * @author chenxb
 */
namespace Driver;

class Driver
{
    /**
     * 连接池
     * @var array
     */
    protected static $instance;
    
    /**
     * 错误信息
     * @var \Exception
     */
    protected $error;
    
    /**
     * 禁止创建对象
     */
    protected function __construct()
    {
    }
    
    /**
     * 禁止克隆对象
     * @return void
     */
    protected final function __clone()
    {
    }
    
    /**
     * 设置错误信息
     * @param \Exception $e
     */
    protected function setError(\Exception $e)
    {
    	$this->error = $e;
    }
    
    /**
     * 获取异常信息
     * @return Exception
     */
    protected function getError()
    {
    	return $this->error;
    }
    
    /**
     * 单例模式创建连接池对象
     * @param array 数组配置
     * @return \Driver
     */
    public static function getInstance(array $driver)
    {
        // 计算hash值
        $key = sprintf("%u", crc32(implode(':', $driver)));
        // 是否已经创建过单例对象
        empty(static::$instance[$key]) AND (static::$instance[$key] = new static($driver));
        // 返回对象
        return static::$instance[$key];
    }
}