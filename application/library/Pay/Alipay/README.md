
require('Alipay/Alipay.php');<br/>
$apliay = new Alipay\Alipay('合作者身份ID', '签名密钥');	// 两个参数都在alipay官方网站获取

1. 即时到账:<br/>
1.1 echo $apliay->transferAccount(array $data); // $data请参照transferAccount方法的参数列表<br/>
1.2 回调操作方式如下<br/>
try<br/>
{<br/>
	$result = $apliay->verify($cacert密钥文件地址);	// 检查数据来源是否合法<br/>
	$status = isset($_REQUEST['trade_status']) ? $_REQUEST['trade_status'] : Null;	// 检查是否支付宝已经交易成功<br/>
	if($result && in_array($status, array('TRADE_FINISHED', 'TRADE_SUCCESS')))<br/>
	{<br/>
		// 其他回传参数请自行查看: print_r($_REQUEST);<br/>
		echo '操作成功!';<br/>
	}<br/>
	else<br/>
	{<br/>
		echo '操作失败';<br/>
	}<br/>
}<br/>
catch(\Exception $e)<br/>
{<br/>
	// 请求支付宝接口进行验证失败,请自行解决业务逻辑<br/>
}<br/>

-----------------------------------------------------------------------------------------------------------------------------------

2. 批量付款到支付宝账户<br/>
2.1 echo $alipay->batchPayment(array $data); // $data请参照batchPayment方法的参数列表<br/>
2.2 回调操作方式如下<br/>
try<br/>
{<br/>
	$result = $apliay->verify($cacert密钥文件地址);	// 检查数据来源是否合法<br/>
	// 其他回传参数请自行查看: print_r($_REQUEST);<br/>
}<br/>
catch(\Exception $e)<br/>
{<br/>
	// 请求支付宝接口进行验证失败,请自行解决业务逻辑<br/>
}<br/>