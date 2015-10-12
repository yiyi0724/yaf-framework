<?php

namespace Image;

class Captcha
{
    protected static $fonts = array(
        'telefono.ttf',
        'brokenrecords_33.ttf',
        'brokenrecords_45.ttf',
    );
    
    /**
     * 输出验证码
     * @param int 长度
     * @param int 宽度
     * @param string 字体
     */
    public static function draw($width, $height, $font=NULL)
    {
        // 随机码
        $randomCode = self::randomCode();
        // 画布
        $image = imagecreatetruecolor($width, $height);
        // 颜色
        $color = imagecolorallocate($image, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        // 填充颜色
  		imagefilledrectangle($image, 0, $height, $width, 0, $color);
  		// 填充文字
  		for($i=0; $i<4; $i++)
  		{
  		    $color = imagecolorallocate($image, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
  		    imagettftext($image, $height*0.5, mt_rand(-30,30), $width/5*$i+mt_rand(1,5)+$width/8, $height/1.4, $color , self::font($font), $randomCode[$i]);
  		}
  		$_SESSION['captcha'] = $randomCode;
  		//线条
  		for($i=0; $i<6; $i++)
  		{
  		    $color = imagecolorallocate($image, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
  		    imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $color);
        }
        //雪花
        $number = $height*0.8;
        for($i=0; $i<$number; $i++)
        {
        $color = imagecolorallocate($image, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
        imagestring($image, mt_rand(1,5), mt_rand(0,$width), mt_rand(0,$height), '*', $color);
        }
        // 输出
        header('Content-type: image/png');
        imagepng($image);
        // 销毁
    	imagedestroy($image);
    }
    
    /**
     * 获取随机码
     * @return string
     */
    protected static function randomCode()
    {
        return substr(str_shuffle("abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789"), 0, 6);
    }
    
    /**
     * 选择何种字体
     * @param string 字体路径 $font
     */
    protected static function font($font) {    
        return $font ? : __DIR__.'/Fonts/'.self::$fonts[mt_rand(0, count(self::$fonts)-1)];
    }
}