<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-13
 * Time: 下午2:30
 */
class ManagerController extends AdminController {

    /**
     * 管理员首页
     */
    public function indexAction() {
        $adminModel = new \AdminModel();
        $this->assign('adminLists', $adminModel->getLists());
    }
}