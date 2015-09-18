<?php

class Ip
{
    /**
     * 获取客户端的IP
     * @param boolean $ip2long 是否转换成为整形
     * @return int|string
     */
    public static function get($ip2long=false)
    {
        if(isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_REAL_IP']))
            {
                $ip = $_SERVER ['HTTP_X_REAL_IP'];
            }
            else if (isset($_SERVER ['HTTP_X_FORWARDED_FOR']))
            {
                $ip = array_pop(explode(',', $_SERVER ['HTTP_X_FORWARDED_FOR']));
            }
            elseif (isset($_SERVER ['HTTP_CLIENT_IP']))
            {
                $ip = $_SERVER ['HTTP_CLIENT_IP'];
            }
            else
            {
                $ip = $_SERVER ['REMOTE_ADDR'];
            }
        }
        else
        {
            if (getenv('HTTP_X_REAL_IP'))
            {
                $ip = getenv('HTTP_X_REAL_IP');
            }
            else if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $ip = array_pop(explode(',', getenv('HTTP_X_FORWARDED_FOR')));
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $ip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $ip = getenv('REMOTE_ADDR');
            }
        }
    
        return $ip2long ? sprintf("%u", ip2long($ip)) : $ip;
    }
}