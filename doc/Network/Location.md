## 页面跳转
```php
// 带HTTP_REFERE的get跳转方式
\Network\Location::get(string $url);

// 带HTTP_REFERE的post跳转方式
\Network\Location::post(string $url, array $data);

// http协议进行的跳转
\Network\Location::redirect($url, int $code=NULL); //如果要进行301,302,303,307跳转，则输入$code
```