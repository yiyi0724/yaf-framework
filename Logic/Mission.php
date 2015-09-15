<?php

namespace Logic;

use \Core\Logic;

class Mission extends Logic
{
    /**
     * 获取我的投注信息
     */
    public function getMyMission($where)
    {
        // 我的信息
        return $this->getMy5755DbMaster()->table('mission')->limit(0, 5)->where($where)->select()->fetchAll();
    }
}