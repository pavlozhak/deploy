<?php

namespace Deploy\Config;

use Deploy\Exception\DeployException;

class Config {

    const CONFIG_FILE = './deploy_config.json';

    public static function get_config() {
        $config_file = file_get_contents(self::CONFIG_FILE);

        if(!$config_file) {
            DeployException::set_exception(Config::class, 'Error read config file [' . self::CONFIG_FILE . ']');
        }

        return json_decode($config_file, FALSE);
    }

}
