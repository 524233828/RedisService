<?php

namespace Home\Service;

class RedisService
{
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var self
     */
    private static $obj;

    private function __construct($host,$port,$auth,$db)
    {
        $this->redis = new \Redis();
        $this->redis->connect($host, $port);

        $this->redis->auth($auth);
        $this->redis->select($db);
    }

    /**
     * 获取当前类实例，单例
     * @return RedisService
     */
    public static function getObj($host,$port,$auth,$db)
    {
        if(self::$obj instanceof self){
            return self::$obj;
        }else{
            return new self($host,$port,$auth,$db);
        }
    }

    /**
     * 缓存并获取数据
     * @param $key
     * @param string $value
     * @param int $ttl
     * @return bool|string
     */
    public function caching($key,$value='',$ttl=0)
    {
        if(!empty($value)){
            $this->redis->set($key,$value,$ttl);
        }

        if(!$this->redis->exists($key)){
            return false;
        }

        return $this->redis->get($key);
    }

    /**
     * 哈希缓存并获取数据
     * @param $key
     * @param string $field
     * @param string $value
     * @param int $expire
     * @return bool|string
     */
    public function hCaching($key,$field="",$value="",$expire=0)
    {

        $is_key = $this->redis->exists($key);
        if(empty($field)){
            return $is_key;
        }

        if(!empty($value)){
            $this->redis->hSet($key,$field,$value);
            if($expire>0){
                $this->redis->expire($key,$expire);
            }
        }

        if(!$this->redis->hExists($key,$field)){
            return false;
        }

        return $this->redis->hGet($key,$field);
    }

    public function getRedis()
    {
        return $this->redis;
    }
}