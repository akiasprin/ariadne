<?php

namespace App\Http\Utilities;

use Closure;
use Exception;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisCacheHelper
{
    static function is_not_json($str) {
        return is_null(json_decode($str));
    }

    static function redis($key, Closure $callback)
    {
        $result = null;
//        try {
//            if (Redis::exists($key)) {
//                $result = json_decode(Redis::get($key));
//            } else {
//                $result = $callback();
//                if (gettype($result) != 'string' && gettype($result) != 'object')
//                    Redis::set($key, json_encode($result));
//                else
//                    Redis::set($key, $result);
//            }
//        } catch (Exception $e) {
//            throw $e;
//        } catch (Throwable $e) {
//            throw $e;
//        }
        $result = $callback();
        return $result;
    }

    static function clean($array)
    {
        foreach ($array as $key) {
            foreach (Redis::keys($key) as $item) {
                Redis::expire($item, -1);
            }
        }
    }
}