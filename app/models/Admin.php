<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-13
 * Time: 下午2:33
 */
use \Tool\Strings;

class AdminModel extends AbstractModel {

    protected $table = 'admin_user';

    public function getLists() {
        return $this->select()->fetchAll();
    }

    /**
     * 管理员加密密码
     * @param string $password 原始密码
     * @return string
     */
    public function enctypePassword($password) {
        return md5(sha1(md5($password, TRUE)) + SECRET_PASSWORD);
    }

    public function format($lists) {
        foreach($lists as &$list) {
            $list['addtime'] = Strings::formatYmdHis($list['addtime']);
        }

        return $lists;
    }
}