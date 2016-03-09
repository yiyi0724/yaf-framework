## 二维码类
[源码地址_非原创](https://github.com/enychen/yaf-framework/blob/master/app/library/Image/QRcode.php)

#### 使用说明
```php
/**
 * @param string $info 二维码信息
 * @param bool $output 是否保存二维码图片文件，默认否
 * @param int $level 表示容错率, (QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）
 * @param int $size 表示生成图片大小，默认是3
 * @param int $margin 表示二维码周围边框空白区域间距值；
 * @param bool $saveandprint 表示是否保存二维码并显示。
 */
\Image\QRcode::png(
    string  $info         = 'text', 
    bool    $output       = false, 
    int     $level        = QR_ECLEVEL_L, 
    int     $size         = 6, 
    int     $margin       = 4, 
    bool    $saveandprint = false
);
```
