<?php

namespace NeoP\Config;

use NeoP\Config\Exception\ConfigException;

class Config extends ConfigProvider
{

    public static function init()
    {
        parent::init();
    }

    public static function get(string $key, $default = NULL)
    {
        try {
            $config = self::getConfig($key);
        } catch (ConfigException $e) {
            $config = $default;
        }
        return $config;
    }

    public static function has(string $key): bool
    {
        try {
            self::getConfig($key);
            return true;
        } catch (ConfigException $e) {
            return false;
        }
    }

    public static function getService(string $key, $default = NULL)
    {
        try {
            $service = parent::getServiceConfig($key);
        } catch (ConfigException $e) {
            $service = $default;
        }
        return $service;
    }
}
