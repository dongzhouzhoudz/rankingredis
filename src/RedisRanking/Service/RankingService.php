<?php
/**
 * Created by PhpStorm.
 * User: dongzhou
 * Date: 2021/1/5
 * Time: 4:06 PM
 */
namespace Redisranking\Service;

use Predis\Client;
use Redisranking\Operation\ZSetOperation;
use Redisranking\Tools\RedisClient;

class RankingService extends ZSetOperation {
    /**
     * @var null
     * 排名服务单例
     */
    private static $rankingService=null;

    /**
     * @param     $host
     * @param int $port
     * @param int $db
     *
     * @return null|RankingService
     * 通过配置实例化 排名redis服务
     */
    public static function getRankServiceInstanceByRedisConfig($host,$port=6379,$db=0){
        if(is_null(self::$rankingService)){
            $redisClient= RedisClient::getClientInstance($host,$port,$db);
            self::$rankingService=new self($redisClient);
        }
        return self::$rankingService;
    }

    /**
     * @param Client $client
     *
     * @return null|RankingService
     * 通过外部predis client 实例化redis服务
     *
     */
    public static function  getRankServiceByRedisClient(Client $client){
        if(is_null(self::$rankingService)){
            self::$rankingService=new self($client);
        }
        return self::$rankingService;

    }


}