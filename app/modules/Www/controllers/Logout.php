<?php
/**
 * 网站登出控制器
 * @author enyccc
 * @version 1.0.0
 */

class LogoutController extends AdminController {

    /**
     * 重写初始化函数
     */
    public function init() {
    }

    public function indexAction() {
        $session = $this->getSession();
        $session->del('admin.uid');
        $session->del('admin.name');
        $session->del('admin.ip');
        $session->del('admin.time');
        $session->del('admin.avatar');
        $session->del('admin.rules');
        $this->redirect('/login/');
    }
}