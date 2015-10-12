<?php

/**
 * 文件上传类
 * @author enychen
 */
namespace File;

class Upload
{
	/**
	 * 上传配置选项
	 * @var array
	 */
	protected $option = array(
		'key'=>null,
		'ext'=>null,
		'size'=>null,
		'filename'=>null,
	);
	
	/**
	 * 构造函数
	 * @param string $_FILES的key $key
	 * @param string 支持的格式,多个用,隔开 $ext
	 * @param int 文件大小,用kb计算 $size
	 * @param string 目标文件 $filename
	 */
	public function __construct($key, $ext, $size, $filename)
	{
		$this->option['key'] = $key;
		$this->option['ext'] = implode(',', $ext);
		$this->option['size'] = $size;
		$this->option['filename'] = $filename;
	}
    
    /**
    * 文件检查
    * @return mixed null | code码
    */
    public function check()
    {
    	// 不存在这个上传
    	if(empty($_FILES[$this->option['key']]))
    	{
    		return 20000;
    	}
    	
    	// 文件
    	$file = $_FILES[$this->option['key']];

    	// 不是post上传,非法来源
    	if(!is_uploaded_file($file['tmp_name']))
    	{
    		return 20008;
    	}
    	
    	// 文件自身错误检查
    	switch($file['error'])
    	{
    		case 0:
    			return null;
    		default:
    			return '2000'.$file['error'];
    			// 1-上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值  			
    			// 2-上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
    			// 3-文件只有部分被上传
    			// 4-文件没有被上传
    			// 6-php设置错误，没有设置临时文件夹
    			// 7-无法将临时文件写入磁盘
    	}
    	
    	// 文件类型检查
    	if($this->option['ext'])
    	{
    		$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
    		$mimeType = $fileInfo->file($file['tmp_name']);
    		if(!in_array($mimeType, $this->option['ext']))
    		{
    			return 20009;
    		}
    	}
	    
    	// 检查文件的大小
    	if($this->option['size'] && $file['size'] > $this->option['size'])
    	{
    		return 20010;
    	}

    	// 未知错误
    	return 20011;
    }
    
    /**
     * 删除临时文件(用于上传失败的时候)
     */
    public function delTmpFile()
    {
    	@unlink($_FILES[$this->option['key']]['tmp_name']);
    }
    
    /**
    * 移动文件
    * @return boolean 移动成功true, 否则false
    */
    public function move()
    {
        return move_uploaded_file($_FILES[$this->option['key']]['tmp_name'], $this->option['filename']);
    }
}

/**
 * 使用方法
 * 
 * 	// 创建对象
 * 	$upload = new Upload(string $key, string $ext, int $size, string $filename);
 * 
 * 	// 错误检查,所有错误的code含义如下
 * 	if($error = $upload->check())
 * 	{
 * 		// 删除临时文件
 * 		$upload->delTmpFile();
 * 
 * 		// 错误输出
 * 		switch($error)
 * 		{
 * 			case 20000:
 *			case 20004:
 * 				//文件没有上传;
 * 				break;
 *			case 20003:
 *				//文件上传不完整;
 *				break;
 *			case 20001:
 *			case 20002:
 *				// 文件大小超过了php.ini设置或者表单中MAX_FILE_SIZE的值
 *				break;
 *			case 20006:
 *			case 20007:
 *				// php设置错误，没有设置临时文件夹 & 无法将临时文件写入磁盘
 *				break;
 *			case 20008:
 *				// 不是post上传,非法来源
 *				break;
 *			case 20009:
 *				// 文件类型不对
 *				break;
 *			case 20010:
 *				// 文件大小超过设置值
 *				break;
 *			case 20011:
 *				// 未知错误
 *				break;
 * 		}	
 * 	}
 * 
 * 	// 文件移动
 * 	$upload->move();
 */