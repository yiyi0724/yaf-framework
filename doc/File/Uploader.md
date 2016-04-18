## 文件上传类
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/File/Uploader.php)

#### 使用说明
- 如果要设置jpg，请使用jpeg名称

#### 内置函数
##### 设置上传文件的属性
```php
/**
 * 用于设置成员属性（$path, $allowtype, $maxsize, $israndname, $newfilename）
 * $path-保存目录 | $allowtype-格式，数组 | $maxsize-最大字节 | $israndname-随机名称 | $newfilename-自定义名称
 * 可以通过连贯操作一次设置多个属性值
 * @param  string $key    成员属性名(不区分大小写)
 * @param  mixed  $val    为成员属性设置的值
 * @return \File\Uploader 返回自己对象$this，可以用于连贯操作
 */
public function set($key, $val);
```

##### 进行上传
```php
/**
 * 进行文件上传
 * @param  string $fileFile  上传文件的表单名称
 * @return bool        		 如果上传成功返回数TRUE
 */
public function upload($fileField);
```

##### 获取上传后的文件名称
```php
/**
 * 获取上传后的文件名称
 * @param  void   没有参数
 * @return string 上传后，新文件的名称， 如果是多文件上传返回数组
 */
public function getFileName();
```

##### 获取上传失败信息
```php
/**
 * 上传失败后，调用该方法则返回，上传出错信息
 * @param  void   没有参数
 * @return string  返回上传文件出错的信息报告，如果是多文件上传返回数组
 */
public function getErrorMsg();
```


#### 完整案例
```php
$uploaderLib = new \File\Uploader();
$uploaderLib->set('path', '/home/eny/Downloads/')     // 保存路径
            ->set('allowtype', ['jpeg'])              // 支持格式，请注意jpg请写成jpeg
            ->set('newfilename', 'name')              // 报名名称，可选用israndname进行随机名，二者选一
            ->set('maxsize', 2048000);                // 允许的大小

// 进行上传处理
if(!$uploaderLib->upload('inputKeyName')) {
  // 上传失败
  exit($uploaderLib->getErrorMsg());
}

// 获取完整文件名
echo $uploaderLib->getFileName();
```
