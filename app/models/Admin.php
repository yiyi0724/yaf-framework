<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-13
 * Time: 下午2:33
 */
class AdminModel extends AbstractModel {

    protected $table = 'admin_user';

    public function getLists() {
        return $this->select()->fetchAll();
    }
}