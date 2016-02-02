# 目录
1. [文件下载](https://github.com/enychen/yaf-framework/tree/master/application/library/File#文件下载)
2. [文件锁](https://github.com/enychen/yaf-framework/blob/master/application/library/File/README.md#%E6%96%87%E4%BB%B6%E4%B8%8B%E8%BD%BD)
  - 内置方法
    - 上锁
    - 解锁
    - 批量解锁

## 文件下载
```php
//创建对象
$download = new \File\Download();
// 设置输出数据
$download->setData(string $data);
// 下载某个文件
//$download->setDataFromFile(string $filename);
// 设置下载的名称
$download->setDownloadName(string $downloadName);
// 设置附加输出的相应头,例如微软的excel, 其它头信息请自行选择,公共头信息已经封装在内部
// $download->setHeader(array("Content-type:application/vnd.ms-excel"));
// 输出
$download->output();
```

## 文件锁
###### 内置方法
上锁：
```php
\File\FileLock::lock(string $filename);
```
解锁：
```php
\File\FileLock::unlock(string $filename, bool $delete = TRUE);
// $delete 解锁后是否删除文件
```
批量解锁
```php
\File\FileLock::unlocks(bool $delete = TRUE);
```

