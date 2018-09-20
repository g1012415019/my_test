<?php

namespace gongzhe\files;


/**
 * 工厂类
 * @author gongzhe
 * @createTime 2018-08-29 09:20:44
 * @qqNumber 1012415019
 * Class Factory
 * @package DawnApi\facade
 */
class Factory
{

    private static $Factory;

    /**
     * Factory constructor.
     */
    private function __construct()
    {
    }

    /**
     * 获取实列
     * @author gongzhe
     * @createTime 2018-08-29 09:20:21
     * @qqNumber 1012415019
     * @param $className 类名
     * @param null $options 构造函数参数
     * @return mixed
     */
    public static function getInstance($className, $options = null)
    {
        if (!isset(self::$Factory[$className]) || !self::$Factory[$className]) {
            self::$Factory[$className] = new $className($options);
        }
        return self::$Factory[$className];
    }
}
