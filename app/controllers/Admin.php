<?php

/**
 * 登录后的控制器
 * @author enychen
 */

use \Network\IP;

abstract class AdminController extends BaseController {

    /**
     * 初始化内容
     */
    public function init() {
        $this->constInit();
        $this->loginInit();
        $this->menusInit();
    }

    /**
     * 登录检查
     */
    public function loginInit() {
        // 检查登录状态
        switch (TRUE) {
            case (!ADMIN_UID):
            case (ADMIN_IP != IP::client()):
            case (ADMIN_TIME + 1800 < time()):
                $this->redirect('/login/');
                break;
        }

        // 更新上次访问时间
        $this->getSession()->set('admin.time', time());
    }

    /**
     * 管理员常量初始化
     */
    public function constInit() {
        $session = $this->getSession();
        define('ADMIN_UID', $session->get('admin.uid'));
        define('ADMIN_NAME', $session->get('admin.name'));
        define('ADMIN_IP', $session->get('admin.ip'));
        define('ADMIN_TIME', $session->get('admin.time'));
    }

    /**
     * 获取左侧菜单栏目
     */
    public function menusInit() {
        $menuModel = new \MenuModel();
        $this->assign('menus', $menuModel->getLists('*'));
    }
}