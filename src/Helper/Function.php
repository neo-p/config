<?php

use NeoP\Config\Config;

if (!function_exists('env')) {
    function env(string $key, $default = NULL)
    {
        $env = getenv($key);
        if ($env) {
            return $env;
        }
        return $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = NULL)
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('service')) {
    function service(string $key, $default = NULL)
    {
        return Config::getService($key, $default);
    }
}