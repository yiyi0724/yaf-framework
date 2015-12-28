
require('Alipay/Alipay.php');
$apliay = new Alipay\Alipay('合作者身份ID', '签名密钥');	// 两个参数都在alipay官方网站获取

1. 即时到账: 
1.1 echo $apliay->transferAccount(array $data); // $data请参照transferAccount方法的参数列表
1.2 回调操作方式如下
try
{
	$result = $apliay->verify($cacert密钥文件地址);	// 检查数据来源是否合法
	$status = isset($_REQUEST['trade_status']) ? $_REQUEST['trade_status'] : Null;	// 检查是否支付宝已经交易成功
	if($result && in_array($status, array('TRADE_FINISHED', 'TRADE_SUCCESS')))
	{
		// 其他回传参数请自行查看: print_r($_REQUEST);
		echo '操作成功!';
	}
	else
	{
		echo '操作失败';
	}
}
catch(\Exception $e)
{
	// 请求支付宝接口进行验证失败,请自行解决业务逻辑
}

-----------------------------------------------------------------------------------------------------------------------------------

2. 批量付款到支付宝账户
2.1 echo $alipay->batchPayment(array $data); // $data请参照batchPayment方法的参数列表
2.2 回调操作方式如下
try
{
	$result = $apliay->verify($cacert密钥文件地址);	// 检查数据来源是否合法
	// 其他回传参数请自行查看: print_r($_REQUEST);
}
catch(\Exception $e)
{
	// 请求支付宝接口进行验证失败,,请自行解决业务逻辑
}