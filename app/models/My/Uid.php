<?php
/**
 * Created by PhpStorm.
 * User: eny
 * Date: 16-10-25
 * Time: 下午4:41
 */
namespace My;

class UidModel extends \AbstractModel {

    /**
     * 表名
     * @var string
     */
    protected $table = 'uid';

    /**
     * 适配器驱动
     * @var string
     */
    protected $adapter = 'my';

}