<?php

/**
 * 文件上传类
 * @author enychen
 */
namespace File;

class Upload
{
	/**
	 * 移动文件
	 * @param string $key 上传文件的key，也就是input的name
	 * @param array $ext 可支持的文件后缀，数组
	 * @param int $siez 限制大小，单位k
	 * @param string $filename 文件移动到哪里去，绝对路径
	 * @throws \Exception 如果文件不符合条件，抛出异常
	 * @return bool
	 */
	public function move($key, array $ext, $size, $filename)
	{
		try
		{
			// 格式化key
			if(empty($_FILES[$key]))
			{
				throw new \Exception('FILE NOT FOUND', 20000);
			}
			
			// 多文件上传
			if(is_array($_FILES[$key]['name']))
			{
				for($i = 0, $len = count($_FILES[$key]['name']); $i < $len; $i++)
				{
					$info[] = array(
						'name'=>$_FILES[$key]['name'][$i],
						'type'=>$_FILES[$key]['type'][$i], 
						'tmp_name'=>$_FILES[$key]['tmp_name'][$i],
						'error'=>$_FILES[$key]['error'][$i], 
						'size'=>$_FILES[$key]['size'][$i]
					);
				}
				
				$_FILES[$key] = $info;
			}
			else
			{
				$_FILES[$key][] = $info;
			}
			
			// 文件检查
			foreach($_FILES[$key] as $file)
			{
				// 不是post上传,非法来源
				if(!is_uploaded_file($file['tmp_name']))
				{
					throw new \Exception('UPLOAD SOURCE NOT LAWFUL', 20008);
				}
				
				// 文件自身错误检查
				if($file['error'])
				{
					throw new \Exception('UPLOAD FILE ERROR', "2000{$file['error']}");
				}
				
				// 文件类型检查
				if($ext)
				{
					$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
					$mimeType = $fileInfo->file($file['tmp_name']);
					if(!in_array($mimeType, $ext))
					{
						throw new \Exception('FILE MIMETYPE ERROR', 20009);
					}
				}
				
				// 检查文件的大小
				if($this->option['size'] && $file['size'] > $this->option['size'])
				{
					return 20010;
				}
			}
			
			// 移动
			foreach($_FILES[$key] as $key=>$file)
			{
				move_uploaded_file($file['tmp_name'], $filename[$key]);
			}
		}
		catch(\Exception $e)
		{
			foreach($_FILES[$key] as $file)
			{
				@unlink($file['tmp_name']);
			}
			
			// 抛出错误给逻辑处理
			throw new \Exception($e->getMessage(), $e->getCode());
		}
		
		return TRUE;
	}
}
