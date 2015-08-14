<?php

namespace Logic;

use \Core\Logic;

class Loto extends Logic
{
    /**
     * 获取我的投注信息
     */
    public function getMyBetInfo($where)
    {
        //define('DEBUG_SQL', 1);
        
        // my5755数据库
        $my5755 = $this->getMy5755Db();
        
        // platform数据库
        $platform = $this->getPlatformDb();
        
        // 我的信息
        $mybets = $my5755->table('loto_userbets')->limit(5)->where($where)->select()->fetchAll();
        
        if($bids = $this->getFileds($mybets, 'bid'))
        {
            // 插入bet
            if($bets = $platform->field('id, bet')->table('loto_bets')->where(['id'=>$bids])->select()->fetchAll())
            {
                $this->auxiliary($mybets, $bets, ['bid'=>'id']);
            }
        }
        
        echo '<pre>';
        print_r($mybets);
        exit;
        
        return $mybets;
    }
}