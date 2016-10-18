<?php

/**
 * 登录后的控制器
 * @author enychen
 */

abstract class AdminController extends BaseController {

    /**
     * 初始化内容
     */
    public function init() {
        $this->menus();
    }

    /**
     * 获取左侧菜单栏目
     */
    public function menus() {
        $menuModel = new \MenuModel();
        $this->assign('menus', $menuModel->getLists('*'));
    }
}