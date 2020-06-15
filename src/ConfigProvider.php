<?php

namespace NeoP\Config;

use NeoP\Config\ConfigRegister;
use NeoP\Config\Exception\ConfigException;

class ConfigProvider
{

    protected const DELIMITER = ".";

    protected static $service = [];

    protected static $config = [];

    protected static function init()
    {
        ConfigRegister::init()->register();
    }

    protected static function config(array $config, string $key = NULL)
    {
        if ($key == NULL) {
            return $config;
        }

        $keys = explode(self::DELIMITER, $key);

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                throw new ConfigException("Configuration does not exist! for: " . $key);
            }
            $config = $config[$k];
        }
        return $config;
    }

    public static function getConfig(string $key = NULL)
    {
        return self::config(self::$config, $key);
    }

    public static function setConfig(array $config)
    {
        self::$config = $config;
    }


    public static function setServiceConfig(array $config) {
        self::$service = $config;
    }

    public static function getServiceConfig(string $key = NULL) {
        return self::config(self::$service, $key);
    }
}
