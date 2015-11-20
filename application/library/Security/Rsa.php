<?php

/**
 * 
 * 1、加密解密的第一步是生成公钥、私钥对，私钥加密的内容能通过公钥解密（反过来亦可以）
 * 	  下载开源RSA密钥生成工具openssl（通常Linux系统都自带该程序），解压缩至独立的文件夹，进入其中的bin目录，执行以下命令：
 * 		openssl genrsa -out rsa_private_key.pem 1024
 * 		openssl pkcs8 -topk8 -inform PEM -in rsa_private_key.pem -outform PEM -nocrypt -out private_key.pem
 * 		openssl rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
 */

class Rsa
{
	protected $privateResource = null;
	
	protected $publicResource = null;
	
	protected $encrypted = '';
	
	protected $decrypted = '';
	
	protected $origin = null;
	
	public function __construct($publicKey, $privateKey, $origin)
	{
		$this->privateResource = openssl_pkey_get_private($privateKey);
		$this->publicResource = openssl_pkey_get_public($publicKey);
		$this->origin = $origin;
	}
	
	/**
	 * 
	 * @param string $flag
	 */
	public function encrypt($flag=false)
	{
		$data = $flag ? $this->privateEncrypt() : $this->publicEncrypt();
		return base_encode($data);
	}
	
	public function decrypt($flag=true) {
		$this->origin = base64_decode($this->origin);
		return $flag ? $this->privateDecrypt() : $this->publicDecrypt();
	}
	
	/**
	 * 私钥加密
	 */
	public function privateEncrypt()
	{
		return openssl_private_encrypt($this->origin, $this->encrypted, $this->privateResource);
	}
	
	/**
	 * 公钥加密
	 */
	public function publicEncrypt()
	{
		return openssl_public_encrypt($this->origin, $this->encrypted, $this->publicResource);
	}
	
	/**
	 * 公钥解密
	 */
	public function publicDecrypt()
	{
		return openssl_public_decrypt($this->origin, $this->decrypted, $this->publicResource);
	}
	
	/**
	 * 私钥解密
	 */
	public function privateDecrypt()
	{
		return openssl_private_decrypt($this->origin, $this->decrypted, $this->privateResource);
	}
} 