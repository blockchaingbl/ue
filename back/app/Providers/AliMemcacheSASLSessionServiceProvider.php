<?php

namespace App\Providers;

use Session;
use App\Library\MemcacheSASL;
use Illuminate\Cache\MemcachedStore;
use Illuminate\Cache\Repository;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\SessionServiceProvider as ServiceProvider;

class AliMemcacheSASLSessionServiceProvider extends ServiceProvider {
    public function boot() {
        Session::extend('ali_memcached', function($app) {
        	
            $minutes = $app['config']['session.lifetime'];
            
            //memcached实例
            $config = config("cache.stores.ali_memcached");
            $memcached = new MemcacheSASL;
            
            foreach ($config['servers'] as $server) {
            	$memcached->addServer(
            			$server['host'], $server['port']
            	);
            }
            $memcached->setSaslAuthData($config['username'], $config['password']);
            
            //创建 MemcachedStore 对象
            $store = new MemcachedStore($memcached);
            
            $repository = new Repository($store);

            //end
            
            return new CacheBasedSessionHandler($repository, $minutes);
        });
    }
}
