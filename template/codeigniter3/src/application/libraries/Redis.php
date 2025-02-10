<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

class Redis
{

    protected $client;

    public function __construct()
    {
        $this->client = new Predis\Client([
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
            'password' => 'secretpwd',
            'timeout' => 5,
            'retry_interval' => 5
        ]);
    }

    public function getCache($key)
    {
        try {
            if (!$this->isRedisConnected()) {
                throw new Exception("Redis not connected");
            }
            $data = $this->client->get($key);
            return $data ?? null;
        } catch (Exception $e) {
            error_log("⚠️ Redis error in getCache: " . $e->getMessage());
            return null;
        }
    }

    public function setCache($key, $value, $expiration = 3600)
    {
        try {
            if (!$this->isRedisConnected()) {
                throw new Exception("Redis not connected");
            }
            return $this->client->setex($key, $expiration, $value);
        } catch (Exception $e) {
            error_log("⚠️ Redis error in setCache: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCache($key)
    {
        try {
            if (!$this->isRedisConnected()) {
                throw new Exception("Redis not connected");
            }
            return $this->client->del([$key]);
        } catch (Exception $e) {
            error_log("⚠️ Redis error in deleteCache: " . $e->getMessage());
            return false;
        }
    }

    public function increaseVersion($key)
    {
        try {
            if (!$this->isRedisConnected()) {
                throw new Exception("Redis not connected");
            }
            return $this->client->incr($key);
        } catch (Exception $e) {
            error_log("⚠️ Redis error in increaseVersion: " . $e->getMessage());
            return false;
        }
    }

    public function isRedisConnected()
    {
        try {
            return $this->client && $this->client->ping() ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }
}