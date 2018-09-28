<?php

namespace App\Providers;

use Cache;
use App\Library\MemcacheSASL;
use Illuminate\Cache\CacheServiceProvider as ServiceProvider;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Cache\Repository;

class AliMemcacheSASLCacheServiceProvider extends ServiceProvider {
    public function boot() {
        Cache::extend('ali_memcached', function($app) {
            $config = config("cache.stores.ali_memcached");
            $memcached = new MemcacheSASL;



            foreach ($config['servers'] as $server) {
                $memcached->addServer(
                    $server['host'], $server['port']
                );
            }
            $memcached->setSaslAuthData($config['username'], $config['password']);

            //创建 MemcachedStore 对象
            $store = new MemcachedStore($memcached, $app['config']['cache']['prefix']);

            $repository = new Repository($store);

            return $repository;
        });
    }
}
