## 缩略图类
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Image/Thumbnail.php)

#### 使用说明：
1. 如果保存图片格式为jpg，请设置成jpeg
2. 请优先了解set方法的使用说明

#### 内置函数

###### 设置缩略图属性
```php
/**
 * 连贯操作成员属性($path, $distwidth, $distheight, $srcfilename, $distfilename, $disttype);
 * $distwidth-缩略图宽度 | $distheight-缩略图高度 | $srcfilename-原图绝对路径
 * $path-缩略图保存目录 |$disttype-缩略图格式 | $distfilename-缩略图名称，注意不加后缀
 * @param string $key 属性名
 * @param mixed $val 属性值
 * @return \Image\Thumbnail 可以进行连续操作
 */
public function set($key, $val);
```

###### 创建缩略图
```php
/**
 * 创建缩略图
 * @return bool 创建成功返回TRUE
 */
public function create();
```

###### 获取完整目标缩略图名称
```php
/**
 * 获取完整目标缩略图名称
 * @return string
 */
public function getDistFileName();
```

###### 获取错误信息
```php
/**
 * 获取错误信息
 * @return string
 */
public function getErrorMsg();
```


#### 完整案例
```php
// 创建对象
$thumbLib = new \Image\Thumbnail();
$thumbLib->set('path', '/home/eny/Downloads/')              // 保存目录
         ->set('srcfilename', '/home/eny/Downloads/1.jpg')  // 原图绝对路径
         ->set('distfilename', 'savename')                  // 保存名称，不带格式后缀名
         ->set('disttype', 'png')                           // 保存图片格式，不设置的话和原图格式一致
         ->set('distwidth', 100)                            // 缩略图图片宽度，不设置根据高度计算，都不设置使用原始大小
         ->set('distheight', 100);                          // 缩略图图片高度，不设置根据宽度计算，都不设置使用原始大小

// 生成结果判断
if(!$thumbLib->create()) {
    // 输出错误结果
	exit($thumbLib->getErrorMsg());
}

// 获取完整保存图片名称
echo $thumbLib->getDistFileName();
```
