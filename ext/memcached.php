<?php

/**
 * Memcached Extension
 *
 * Copyright 2018 tggtzbh <tggtzbh@sina.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ext;

class memcached
{
    /**
     * Default settings for Memcache
     */
    public static $host     = '127.0.0.1';
    public static $port     = 11211;
    public static $compress = false;
    public static $timeout  = 1;

    private static $memcached = null;

    /**
     * Connect Memcache
     *
     * @return \Memcached
     * @throws \Exception
     */
    private static function connect(): \Memcached
    {
        $mem = new \Memcached();
        $mem->addServer(self::$host, self::$port);
        $mem->setOption($mem::OPT_COMPRESSION, self::$compress);
        $mem->setOption($mem::OPT_CONNECT_TIMEOUT, self::$timeout * 1000);
        if ($mem->getStats() === false) {
            throw new \Exception('Memcached: Host or Port ERROR!');
        }
        self::$memcached = $mem;
        return $mem;
    }

    /**
     * Get cache
     *
     * @param $key
     *
     * @return null
     * @throws \Exception
     */
    public static function get($key)
    {
        if (self::$memcached === null) {
            self::connect();
        }
        $mem = self::$memcached;
        $ret = $mem->get($key);
        if ($mem->getResultCode() === $mem::RES_NOTFOUND) {
            $ret = null;
        }
        return $ret;
    }

    /**
     * Set cache
     *
     * @param $key
     * @param $value
     *
     * @return bool
     * @throws \Exception
     */
    public static function set($key, $value): bool
    {
        if (self::$memcached === null) {
            self::connect();
        }
        $mem = self::$memcached;
        return $mem->set($key, $value);
    }
}