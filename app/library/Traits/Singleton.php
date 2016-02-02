<?php
/**
 * 单例模式
 * @author chenxb
 */
namespace Traits;

trait Singleton
{
    /**
     * 对象池
     * @var array
     */
    protected static $instance;
    
    /**
     * 禁止直接创建构造函数
     * @param array $driver
     */
    protected function __construct($driver) {
    	$this->create($driver);
    }
    
    /**
     * 禁止克隆对象
     * @return void
     */
    protected final function __clone()
    {
    }
    
    /**
     * 单例模式创建连接池对象
     * @param array 数组配置
     * @return \Driver
     */
    public static function getInstance(array $driver)
    {
        // 计算hash值
        $key = static::hashValue($driver);
        // 是否已经创建过单例对象
        if(empty(static::$instance[$key]))
        {
        	static::$instance[$key] = new static($driver);
        }
        // 返回对象
        return static::$instance[$key];
    }
    
    protected static function hashValue(array $driver)
    {
    	$hash = array();
    	foreach($driver as $key=>$value)
    	{
    		$hash[$key] = is_array($value) ? static::hashValue($value) : $value;
    	}
    	
    	return sprintf("%u", crc32(implode(':', $hash)));
    }
}