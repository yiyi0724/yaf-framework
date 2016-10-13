<?php

/**
 * 登录后的控制器
 * @author enychen
 */
namespace base;

use EasyWeChat\Menu\Menu;

abstract class AdminController extends BaseController {

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