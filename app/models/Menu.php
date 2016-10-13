<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-13
 * Time: 下午1:35
 */

class MenuModel extends AbstractModel {

    /**
     * 根据管理员权限获取栏目操作
     * @param string $rule 用户权限列表字符串,用,隔开
     * @return array 栏目信息
     */
    public function getLists($rule) {
        // 获取栏目信息
        $table = $this->T('admin_menu');
        if($rule != '*') {
            $table->where("id in({$rule})");
        }
        $lists = $table->order('parent ASC, sort ASC')->select()->fetchAll();

        // 格式化栏目信息
        $menus = array();
        foreach($lists as $list) {
            if($list['parent'] == 0) {
                $menus[$list['id']] = $list;
                $menus[$list['id']]['children'] = array();
                $menus[$list['id']]['opened'] = FALSE;
            } else {
                $menus[$list['parent']]['children'][] = $list;
            }
        }

        return $menus;
    }

    /**
     * 判断管理员是否拥有权限
     */
    public function isPower() {

    }
}