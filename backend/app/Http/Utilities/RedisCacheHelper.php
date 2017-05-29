<?php

namespace App\Http\Utilities;

use Closure;
use Exception;
use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisCacheHelper
{
    static function redis(String $key, Closure $callback) {
        $result = null;
        try {
            if (Redis::exists($key)) {
                $result = json_decode(Redis::get($key));
            } else {
                $result = $callback();
                Redis::set($key, $result);
            }
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
        return $result;

    }
}