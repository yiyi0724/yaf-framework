<?php

/**
 * 所有模块控制基类的基类
 * @author enychen
 */

use \Tool\Is;
use \Yaf\Session;
use \Traits\Request;
use \Yaf\Application;
use \Exceptions\Multi;
use \Exceptions\Notify;
use \Exceptions\NotFound;
use \Exceptions\Redirect;
use \Exceptions\Forbidden;
use \Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract {

    /**
     * 获取经过验证请求对象
     * @return \traits\Request 请求封装对象
     */
    public final function getRequest() {
        return Request::getInstance();
    }

    /**
     * 获取配置对象
     * @return \Yaf\Config_Abstract
     */
    public final function getConfig() {
        return Application::app()->getConfig();
    }

    /**
     * 获取session对象
     * @return \Yaf\Session
     */
    public final function getSession() {
        return Session::getInstance();
    }

    /**
     * 视图参数绑定
     * @param string $key 键
     * @param mixed $value 值
     * @return void
     */
    public final function assign($key, $value) {
        $this->getView()->assign($key, $value);
    }

    /**
     * json或jsonp输出
     * @param boolean $status 结果状态
     * @param string $message 提示信息
     * @param array $data 数据信息
     * @param int $code 提示码
     * @return void
     */
    public final function json($status, $message, $code, $data = NULL) {
        $json['status'] = $status;
        $json['message'] = $message;
        $json['data'] = $data;
        $json['code'] = $code;
        $json = json_encode($json);
        if (isset($_REQUEST['callback']) && Is::jsonp($_REQUEST['callback'])) {
            exit("<script type='text/javascript'>{$_REQUEST['callback']}({$json})</script>");
        } else {
            header("Content-type: application/json; charset=UTF-8");
            exit($json);
        }
    }

    /**
     * 输出调试信息
     * @param mixed $content 调试内容
     * @return void
     */
    protected final function debug($content) {
        exit(sprintf("<pre>%s</pre>", print_r($content, TRUE)));
    }

    /**
     * 抛出403的异常
     * @param number $code 错误码
     * @param string $message 错误信息
     * @throws Forbidden
     * @return void
     */
    protected final function throwForbiddenException($code, $message) {
        throw new Forbidden($message, $code);
    }

    /**
     * 抛出404异常
     * @param number $code 错误码
     * @param string $message 错误信息
     * @throws NotFound
     * @return void
     */
    protected final function throwNotFoundException($code, $message) {
        throw new NotFound($message, $code);
    }

    /**
     * 抛出错误通知的异常
     * @param number $code 错误码
     * @param string $message 错误信息
     * @throws Notify
     * @return void
     */
    protected final function throwNotifyException($code, $message) {
        throw new Notify($message, $code);
    }

    /**
     * 抛出进行跳转的异常
     * @param number $code 错误码
     * @param string $message 错误信息
     * @throws Redirect
     * @return void
     */
    protected final function throwRedirectException($code, $message) {
        throw new Redirect($message, $code);
    }

    /**
     * 抛出多条错误的异常
     * @param int $code 错误码
     * @param array $message 错误信息
     * @throws Multi
     * @return void
     */
    protected final function throwMultiException($code, array $message) {
        throw new Multi($message, $code);
    }
}