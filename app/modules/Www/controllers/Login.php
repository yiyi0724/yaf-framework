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
            $this->json(FALSE, '手机号或密码格式不正确', 20001);
        }

        // 用户检查
        $adminModel = new \AdminModel();
        $adminArr = $adminModel->where('username=:u and password=:p and status=1',
            $username,$adminModel->enctypePassword($password))->limit(1)->select()->fetchRow();
        if(!$adminArr) {
            $this->json(FALSE, '手机号或密码错误', 20002, $adminModel->enctypePassword($password));
        }

        // 获取用户权限
        $groupModel = new \GroupModel();
        $groupArr = $groupModel->where('id=:id', $adminArr['group_id'])->select()->fetchRow();

        // 计算用户权限
        if($groupArr['id'] != '*') {
            $rules = $adminArr['attach_rules'] ? explode(',', $adminArr['attach_rules']) : array();
            $rules = implode(',', array_merge(explode(',', $groupArr['rules']), $rules));
        }

        // 保存用户权限
        $session = $this->getSession();
        $session->set('admin.uid', $adminArr['id']);
        $session->set('admin.name', $adminArr['nickname']);
        $session->set('admin.ip', IP::client());
        $session->set('admin.time', time());
        $session->set('admin.avatar', $adminArr['avatar']);
        $session->set('admin.rules', implode(',', $rules));

        // 登录成功返回
        $this->json(TRUE, '登录成功', 20010);
    }
}