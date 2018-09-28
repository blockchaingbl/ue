<?php
namespace App;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

class NhdApplication extends Application{

    public function any($uri, $action) {
        $this->addRoute('POST', $uri, $action);
        $this->addRoute('GET', $uri, $action);
        return $this;
    }


    public function getCurrentRoute()
    {
        return $this->currentRoute[0]>0?$this->currentRoute[1]:null;
    }

    /**
     * Register the facades for the application.
     *
     * @return void
     */
    public function withFacades()
    {
        Facade::setFacadeApplication($this);

        if (! static::$aliasesRegistered) {
            static::$aliasesRegistered = true;

            class_alias('Illuminate\Support\Facades\App', 'App');
            class_alias('Illuminate\Support\Facades\Auth', 'Auth');
            //class_alias('Illuminate\Support\Facades\Bus', 'Bus');
            class_alias('Illuminate\Support\Facades\DB', 'DB');
            class_alias('Illuminate\Support\Facades\Cache', 'Cache');
            class_alias('Illuminate\Support\Facades\Cookie', 'Cookie');
            //class_alias('Illuminate\Support\Facades\Crypt', 'Crypt');
            //class_alias('Illuminate\Support\Facades\Event', 'Event');
            //class_alias('Illuminate\Support\Facades\Hash', 'Hash');
            class_alias('Illuminate\Support\Facades\Log', 'Log');
            //class_alias('Illuminate\Support\Facades\Mail', 'Mail');
            //class_alias('Illuminate\Support\Facades\Queue', 'Queue');
            class_alias('Illuminate\Support\Facades\Request', 'Request');
            class_alias('Illuminate\Support\Facades\Config','Config');
            //class_alias('Illuminate\Support\Facades\Schema', 'Schema');
            class_alias('Illuminate\Support\Facades\Session', 'Session');
            //class_alias('Illuminate\Support\Facades\Storage', 'Storage');
            class_alias('Illuminate\Support\Facades\Validator', 'Validator');

            class_alias('App\Helper', 'Helper');
        }
    }

    /**
     * Load a configuration file into the application.
     *
     * @param  string  $name
     * @return void
     */
    public function loadConfig($name,$as="")
    {
        if(!$as)$as=$name;
        if(isset($GLOBALS['nhd_config'][$name]))
        {
            if (isset($this->loadedConfigurations[$as])) {
                return;
            }

            $this->loadedConfigurations[$as] = true;
            $this->make('config')->set($as,$GLOBALS['nhd_config'][$name]);
        }
    }

}
