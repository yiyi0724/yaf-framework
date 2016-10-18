<?php

/**
 * 自定义请求对象
 * @author enyccc
 * @version 1.0.0
 */
namespace Traits;

use \Yaf\Application;

class Request {

    /**
     * 单例对象
     * @var \traits\Request
     */
    protected static $instance;

    /**
     * yaf请求对象
     * @var \Yaf\Request_Abstract
     */
    protected $yafRequest = NULL;

    /**
     * 参数列表
     * @var array
     */
    protected $params = array();

    /**
     * 禁止单例对象
     */
    protected final function __construct() {
        // PUT和DETELE方法支持
        if (($putOrDelete = array()) || IS_PUT || IS_DELETE) {
            parse_str(file_get_contents('php://input'), $putOrDelete);
        }

        // 输入数据源
        $this->setYafRequest();
        $params = array_merge($this->getYafRequest()->getParams(), $putOrDelete, $_REQUEST);

        // 获取检查规则
        $xml = sprintf('%sforms%s%s%s%s.xml', MODULE_PATH, DS, strtolower(CONTROLLER_NAME), DS, strtolower(ACTION_NAME));
        if (is_file($xml)) {
            // 读取xml文件
            $simpleXMLElements = @simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$simpleXMLElements) {
                throw new \Exception("{$xml}语法有误");
            }
            // 进行表单检查
            $fromTrait = new Form($params, $this->getYafRequest()->getMethod());
            $fromTrait->useXmlRule($simpleXMLElements)->filter();
            $params = $fromTrait->getSuccess();
        }

        $this->setParams($params);
    }

    /**
     * 禁止克隆对象
     * @return void
     */
    protected final function __clone() {
    }

    /**
     * 获取单例请求对象
     * @return Request $this 单例请求对象
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置yaf的请求对象
     * @return Request $this 返回当前对象进行连贯操作
     */
    protected function setYafRequest() {
        $this->yafRequest = Application::app()->getDispatcher()->getRequest();
        return $this;
    }

    /**
     * 获取yaf的请求对象
     * @return \Yaf\Request_Abstract
     */
    public function getYafRequest() {
        return $this->yafRequest;
    }

    /**
     * 设置所有的传递参数
     * @param array $params 传递参数
     * @return Request $this 返回当前对象进行连贯操作
     */
    protected function setParams($params) {
        $this->params = $params;
        return $this;
    }

    /**
     * 获取所有的传递参数
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * 获取参数
     * @param string $key 参数键
     * @param mixed $default 找不到默认返回值
     * @return mixed
     */
    public function get($key, $default = NULL) {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * 获取server信息
     * @param string $key 键名
     * @param mixed $default 找不到默认返回值
     * @return mixed
     */
    public function getServer($key, $default = NULL) {
        $result = $this->getServer($key);
        return $result ?: $default;
    }

    /**
     * 获取完整url路径
     * @param string $encode 是否进行编码，默认编码
     * @return string
     */
    protected function getFullUrl($encode = TRUE) {
        $url = "{$this->getServer('REQUEST_SCHEME', 'http')}://";
        $url .= "{$this->getServer('SERVER_NAME')}{$this->getServer('REQUEST_URI')}";
        return $encode ? urlencode($url) : $url;
    }

    /**
     * 回调yaf内置的方法
     * @param string $method 请求的方法
     * @param array $args 附加参数
     * @return mixed
     */
    public function __call($method, $args) {
        return call_user_func_array(array($this->getYafRequest(), $method), $args);
    }
}