<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-13
 * Time: 下午2:33
 */
class AdminModel extends AbstractModel {

    public function getLists() {
        return $this->T('admin_user')->select()->fetchAll();
    }
}