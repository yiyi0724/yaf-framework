<?php
/**
 * 网站登录控制器
 * @author enychen
 */
use \Network\IP;

class LoginController extends AdminController {

    public function init() {
        $this->constInit();
        ADMIN_UID and $this->redirect('/');
    }

    /**
     * 登录首页
     */
    public function indexAction() {
    }

    /**
     * 进行登录
     */
    public function todoAction() {
        // 参数检查
        try {
            $request = $this->getRequest();
            $username = $request->get('username');
            $password = $request->get('password');
        } catch(\Exceptions\Multi $e) {
            $this->json(FALSE, '手机号或密码格式不正确', 10102);
        }

        // 用户检查
        $adminModel = new \AdminModel();
        $adminArr = $adminModel->where('username=:u and password=:p and status=1',
            $username,$adminModel->enctypePassword($password))->limit(1)->select()->fetchRow();
        if(!$adminArr) {
            $this->json(FALSE, '账号或密码错误', 10101, $adminModel->enctypePassword($password));
        }

        $session = $this->getSession();
        $session->set('admin.uid', $adminArr['id']);
        $session->set('admin.name', $adminArr['nickname']);
        $session->set('admin.ip', IP::client());
        $session->set('admin.time', time());

        $this->json(TRUE, '登录成功', 10001);
    }
}