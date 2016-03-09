## 验证码类使用（基于GD扩展）
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Image/Captcha.php)

#### 完整案例
```php
// 生成验证码并输出
$image = new \Image\Captcha(100, 40);
$image->createImage();
$image->createLine();
$code = $image->getCode();
$image->output();

// 验证码保存到session中
$_SESSION['captcha']['channel'] = $code;

// 结束运行
exit();
```

#### 内置方法
###### 创建对象
```php
/**
 * @param int $width 图片长度，默认100
 * @param int $heigh 图片宽度，默认40
 * @param int $length 字符个数，默认4个字符
 * @return void
 */
$image = new \Image\Captcha(int $width = 100, int $height = 40, int $length = 4);
```

###### 生成干扰星星
```php
/**
 * @param int $star 星星个数，默认100个
 * @return void
 */
$image->setStar(int $star = 100);
```

###### 更改字体大小，默认在创建对象的时候自己计算
```php
/**
 * @param int $fontSize 字体大小值
 * @return void
 */
$image->setFontSize(int $fontSize);
```

###### 生成验证码图片
```php
/**
 * @return void
 */
$image->createImage();
```

###### 获取生成的验证码
```php
/**
 * @return string
 */
$image->getCode();
```

###### 输出验证码图片
```php
/**
 * @return void
 */
$image->output();
```
