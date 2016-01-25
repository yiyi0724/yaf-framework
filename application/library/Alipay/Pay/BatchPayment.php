<?php

/**
 * 批量付款到支付宝账户
 * @author enychen
 * @version 1.0
 * 
 * @link 请求参数说明:				https://doc.open.alipay.com/doc2/detail.htm?spm=0.0.0.0.ACgspp&treeId=64&articleId=103773&docType=1
 * @link 服务器异步通知参数说明:	https://doc.open.alipay.com/doc2/detail.htm?spm=0.0.0.0.Y0WrmN&treeId=64&articleId=103775&docType=1
 */
namespace Alipay\Pay;

class BatchPayment extends Base
{

	/**
	 * 批量付款接口(我司向用户付钱)
	 * @param array 包含列表如下
	 *  $origin['asyncUrl']		必须	异步回调地址
	 *  $origin['order']		必须	批次号, 格式：当天日期[8位]+序列号[3至16位]，如：201512201211
	 *  $origin['price']		必须	付款总金额
	 *  $origin['number']		必须	付款笔数
	 *  $origin['data']			必须	付款详细数据, 格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2
	 *
	 * @return string html表单
	 */
	public function send(array $origin)
	{
		$data['service'] = 'batch_trans_notify';
		$data['email'] = $this->options['email'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['account_name'] = $origin['account'];
		$data['pay_date'] = date('Ymd');
		$data['batch_no'] = $origin['order'];
		$data['batch_fee'] = $origin['price'];
		$data['batch_num'] = $origin['number'];
		$data['detail_data'] = $origin['data'];
		return $this->buildForm($data);
	}

	/**
	 * 回调验证
	 * @return string 验证失败的信息,如果验证成功则返回NULL
	 */
	public function verifyDetail()
	{
	}
}