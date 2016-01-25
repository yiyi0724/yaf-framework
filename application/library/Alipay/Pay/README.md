## 请先修改\Alipay\Pay\Base类的$options配置
 - partner 合作者身份ID
 - email 用户账号
 - signKey 签名密钥
 - signType 签名加密方式,默认使用MD5,目前只支持MD5,请勿修改
 - charset 字符编码, 默认utf-8
 - phishingKey 通过时间戳查询接口获取的加密支付宝系统时间戳, 如果已申请开通防钓鱼时间戳验证，则此字段必填
 - clentIp 用户在创建交易时，该用户当前所使用机器的IP, 如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填
 
## 即时到帐接口
```php
// 付款
$alipay = new \Alipay\Pay\TransferAccount();
$origin['order'] = '订单号';
$origin['name'] = '商品名称';
$origin['price'] = '价格';
$origin['syncUrl'] = '同步跳转地址';
$origin['asyncUrl'] = '异步跳转地址';
// $origin['desc']      可选	商品描述
// $origin['type']		可选	交易类型 1-商品购买, 4-捐赠, 47-电子卡券, 默认是1
// $origin['quantity']  可选	商品数量
// $origin['errorUrl']  可选	请求出错时的通知页面URL地址,错误码参照:http://doc.open.alipay.com/doc2/detail?treeId=62&articleId=103749&docType=1
// $origin['showUrl']	可选	商品显示URL地址
// $origin['other']		可选	其他参数,传递给支付宝后支付宝再回传
// $origin['bank']		可选	使用什么银行支付,不设置默认使用支付宝余额支付
//		                      银行简码——混合渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103763&docType=1
//                             银行简码——纯借记卡渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103764&docType=1

exit($alipay->send($origin));

//-----------------------------------------------------------------------------------------
// 回调验证
try
{
  // 回调验证
  $alipay = new \Alipay\Pay\TransferAccount();
  $alipay->verify();
  
  // 继续处理业务逻辑
}
catch(\Exception $e)
{
  $code = $e->getCode();
  // 20001 回调的数据是空的
  // 20002 sign是错误的
  // 20003 支付宝不存在该交易信息
  // 20004 交易的结果状态不是TRADE_FINISHED和TRADE_SUCCESS，也就是说交易不成功
}

```
