<?php

namespace Ku\Alipay;

use \Ku\Alipay\lib\AlipaySubmit;
use \Ku\Alipay\lib\AlipayNotify;

/**
 * 支付宝
 */
class Alipay {
    
    /**
     * 进行支付
     * @param int 价格
     * @param string 订单号
     * @param string 支付项目
     * @param string 支付描述
     * @param string 商品的显示页面
     * @return string html字符串
     */    
    public static function pay($price, $ordernum, $title, $desc, $showurl) {        
        // 读取配置
        $alipayConfig = \Yaf\Application::app()->getConfig()->get('alipay');
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipayConfig['partner']),
            "seller_email" => trim($alipayConfig['seller_email']),
            "payment_type"	=> 1,                                    // 支付类型
            "notify_url"	=> $alipayConfig['notify_url'],    // 服务器异步通知网址
            "return_url"	=> $alipayConfig['return_url'],       // 服务器同步通知网址
            "out_trade_no"	=> $ordernum,                            //商户订单号, 商户网站订单系统中唯一订单号，必填
            "subject"	=> $title,
            "total_fee"	=> $price,                                      // 付款金额，必填
            "body"	=> $desc,                                                // 付款描述
            "show_url"	=> $showurl,
            "anti_phishing_key"	=> "",                                  //防钓鱼时间戳
            "exter_invoke_ip"	=> "",                                    // 客户端的IP地址
            "_input_charset"	=> trim(strtolower($alipayConfig['input_charset']))
        );
        
        // 生成html内容
        $alipaySubmit = new AlipaySubmit($alipayConfig);
        return $alipaySubmit->buildRequestForm($parameter,'post');
    }
    
    /**
     * 同步验证付款结果
     * @return boolean
     */
    public static function notifyReturn() {
        // 读取配置
        $alipayConfig = \Yaf\Application::app()->getConfig()->get('alipay');
        // 验证来源是否合法
        $alipayNotify = new AlipayNotify($alipayConfig);
        return $alipayNotify->verifyReturn();
    }
    
    /**
     * 异步验证付款结果
     * @return boolean
     */
    public function synchronous() {
        // 读取配置
        $alipayConfig = \Yaf\Application::app()->getConfig()->get('alipay');
        // 验证来源是否合法
        $alipayNotify = new AlipayNotify($alipayConfig);
        return $alipayNotify->verifyNotify();
    }
}