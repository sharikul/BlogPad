<?php

require_once 'phpFastCache/phpfastcache.php';

class Cache {

    static function init() {

        phpFastCache::setup('storage', 'auto');

        return phpFastCache();
    } 
}

?>