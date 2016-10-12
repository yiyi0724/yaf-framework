<?php
/**
 * 网站登录控制器
 * @author enychen
 */

class LoginController extends \base\AdminController {

    /**
     * 登录首页
     */
    public function indexAction() {
    }

    /**
     * 进行登录
     */
    public function todoAction() {
        $this->json(TRUE, '密码错误', 10001);
    }
}