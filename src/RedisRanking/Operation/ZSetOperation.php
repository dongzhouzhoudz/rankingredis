<?php
/**
 * Created by PhpStorm.
 * User: dongzhou
 * Date: 2021/1/5
 * Time: 4:07 PM
 */

namespace Redisranking\Operation;

use Predis\Client;

class ZSetOperation {
    protected $_redisClient;

    public function __construct(Client $redisClient) {
        $this->_redisClient = $redisClient;
    }

    /**
     * @param string $zSetName
     * @param int    $score
     * @param string $key
     *
     * @return int
     * 添加单条分数数据
     */
    public function setOneKeyScore(string $zSetName, int $score, string $key) {
        if ( ! empty($zSetName) && $this->checkIntNumber($score, 1)) {
            $status = $this->_redisClient->zadd($zSetName, [$key => $score]);

            return $status;
        } else {
            return 0;
        }
    }

    /**
     * @param string $zSetName
     * @param array  $batchKeyScore
     *
     * @return int
     * 批量设置分数
     */
    public function setBatchKeyScore(string $zSetName, array $batchKeyScore) {
        if ( ! empty($batchKeyScore) && ! empty($zSetName)) {
            foreach ($batchKeyScore as $key => $score) {
                if ( ! $this->checkIntNumber($score, 1)) {
                    unset($batchKeyScore[$key]);
                }
            }
            $status = $this->_redisClient->zadd($zSetName, $batchKeyScore);

            return $status;
        } else {
            return 0;
        }
    }

    /**
     * @param string $zSetName
     * @param string $key
     * @param int    $changeScore
     *
     * @return string
     * 修改某个key分数
     */
    public function increaseKeyScore(string $zSetName, string $key,
        int $changeScore) {

        if ( ! empty($zSetName) && $this->checkIntNumber($changeScore)) {
            $strResult = $this->_redisClient->zincrby($zSetName, $changeScore,
                $key);

            return $strResult;
        } else {
            return "";
        }
    }

    /**
     * @param string $zSetName
     * @param string $key
     *
     * @return int
     * 删除排行中的某一个排行值
     */
    public function removeZSetKey(string $zSetName, string $key) {
        return $this->_redisClient->zrem($zSetName, $key);
    }


    /**
     * @param string $zSetName
     * @param int    $start 开始
     * @param int    $end 结束 -1 表示最后一位
     * @param array  $options
     *
     * @return array
     * 分数从高到低获取
     */
    public function getKeyScoreFromHighToLow(string $zSetName, int $start = 0,
        int $end = -1, array $options = ["withscores"=>true]) {
        return $this->_redisClient->zrevrange($zSetName, $start, $end,
            $options);
    }

    /**
     * @param string $zSetName
     * @param int    $start 开始
     * @param int    $end   结束 -1 表示最后一位
     * @param array  $options
     *
     * @return array
     *
     */
    public function getKeyScoreFromLowToHigh(string $zSetName, int $start = 0,
        int $end = -1, array $options = ["withscores"=>true]) {
        return $this->_redisClient->zrange($zSetName, $start, $end, $options);
    }


    /**
     * @param string $zSetName
     * @param        $lowScore 最低分数
     * @param        $highScore 最高分数
     * @param bool   $needScore 是否需要返回分数
     * @param int    $offset     起始位置
     * @param int    $count      偏移量
     *
     * @return array
     * 按照分数排列获取分数区间的数据
     */
    public function getKeyScoreByScoreRange(string $zSetName,$lowScore,$highScore,$needScore=true,int $offset = 0 , int $count=-1){

        $options=[];
        if($needScore){
            $options["withscores"]=true;
        }

        if($count!=-1){
            $options['limit']=array($offset,$count);
        }

        return $this->_redisClient->zrangebyscore($zSetName,$lowScore,$highScore,$options);
    }


    /**
     * @param string $zSetName
     * @param string $key
     *
     * @return int|null
     * 分数从高到低的排名
     */
    public function getKeyScoreRankFromHighToLow(string $zSetName,string $key){
        return $this->_redisClient->zrevrank($zSetName,$key);
    }


    /**
     * @param string $zSetName
     * @param String $key
     *
     * @return int|null
     * 分数从低到高的排名
     */
    public function getKeyScoreRankFromLowToHigh(string $zSetName,String $key){
        return $this->_redisClient->zrank($zSetName,$key);
    }





    /**
     * @param     $number
     * @param int $type 0 整数  1 正整数 2 负整数
     *
     * @return bool
     * 判断整数
     */
    public function checkIntNumber($number, $type = 0) {
        if (floor($number) == $number) {
            if ($type == 1) {//正整数
                if ($number > 0) {
                    return true;
                }
            }
            if ($type == 2) {//负整数
                if ($number < 0) {
                    return true;
                }
            }

            return true;
        } else {
            return false;
        }
    }


}