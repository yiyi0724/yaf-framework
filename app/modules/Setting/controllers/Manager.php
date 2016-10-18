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
        $lists = $adminModel->format($adminModel->getLists());
        $this->assign('adminLists', $lists);
    }
}