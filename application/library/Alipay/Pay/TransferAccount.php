<?php

/**
 * 即时到账
 * @author enychen
 * @link 请求参数说明:			 https://doc.open.alipay.com/doc2/detail.htm?spm=0.0.0.0.i0aD04&treeId=62&articleId=103740&docType=1
 * @link 服务器异步通知参数说明: https://doc.open.alipay.com/doc2/detail.htm?spm=0.0.0.0.45S27X&treeId=62&articleId=103743&docType=1
 */
namespace Alipay\Pay;

class TransferAccount extends Base
{

	/**
	 * 即时到账的接口(用户向我司付钱)
	 * @param array 包含列表如下
	 *  $origin['order']		必须	订单号
	 *  $origin['name']			必须	商品名称
	 *  $origin['price']		必须	商品价格或单价,如果存在quantity,则表示单价,否则表示总价
	 *  $origin['syncUrl']		必须	商品购买同步回调URL地址
	 *  $origin['asyncUrl']		必须	商品购买异步回调URL地址
	 *  $origin['errorUrl']		可选	请求出错时的通知页面URL地址,错误码参照:http://doc.open.alipay.com/doc2/detail?treeId=62&articleId=103749&docType=1
	 *  $origin['quantity']		可选	商品数量
	 *  $origin['showUrl']		可选	商品显示URL地址
	 *  $origin['desc']			可选	商品描述
	 *  $origin['type']			可选	交易类型 1-商品购买, 4-捐赠, 47-电子卡券, 默认是1
	 *  $origin['bank']			可选	使用什么银行支付,不设置默认使用支付宝余额支付
	 * 									银行简码——混合渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103763&docType=1
	 * 									银行简码——纯借记卡渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103764&docType=1
	 *  $origin['other']		可选	其他参数,传递给支付宝后支付宝再回传
	 *
	 * @return string html表单
	 */
	public function send(array $origin)
	{
		$data['service'] = 'create_direct_pay_by_user';
		$data['seller_id'] = $this->options['partner'];
		$data['out_trade_no'] = $origin['order'];
		$data['subject'] = $origin['name'];
		$data['return_url'] = $origin['syncUrl'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['payment_type'] = isset($origin['type']) ? $origin['type'] : 1;
		isset($origin['errorUrl']) and $data['error_notify_url'] = $origin['errorUrl'];
		isset($origin['quantity']) and $data['price'] = $origin['price'];
		isset($origin['quantity']) and $data['quantity'] = $origin['quantity'];
		isset($origin['showUrl']) and $data['show_url'] = $origin['showUrl'];
		isset($origin['desc']) and $data['body'] = $origin['desc'];
		empty($origin['quantity']) and $data['total_fee'] = $origin['price'];
		isset($origin['bank']) and $data['defaultbank'] = $origin['bank'] and $data['paymethod'] = 'bankPay';
		isset($origin['other']) and $data['extra_common_param'] = $origin['other'];
		return $this->buildForm($data);
	}

	/**
	 * 回调验证
	 * @return string 验证失败的信息,如果验证成功则返回NULL
	 */
	public function verifyDetail()
	{
		// 交易是否成功
		if(!in_array($_REQUEST['trade_status'], array('TRADE_FINISHED', 'TRADE_SUCCESS')))
		{
			return '支付宝方交易失败';
		}
		
		return NULL;
	}
}