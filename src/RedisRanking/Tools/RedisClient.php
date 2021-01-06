<?php
/**
 * Created by PhpStorm.
 * User: dongzhou
 * Date: 2021/1/6
 * Time: 1:27 PM
 */

namespace Redisranking\Tools;

use Predis\Client;

class RedisClient {
    //Redis Client 单例模式
    private static $_instance = null;

    //私有构造函数 单例
    private function __construct() {
    }

    //获取redis连接client
    public static function getClientInstance($host, $port = 6379, $db = 0) {
        if (is_null(self::$_instance)) {
            $redisConfig = ["scheme" => "tcp", "host" => $host,
                            "port"   => $port,];
            self::$_instance = new Client($redisConfig);
            self::$_instance->select($db);
        }

        return self::$_instance;
    }

}