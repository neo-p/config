<?php 

namespace NeoP\Config;

use Dotenv\Dotenv;
use IteratorIterator;
use DirectoryIterator;
use InvalidArgumentException;
use NeoP\Application;
use NeoP\Config\Exception\ConfigException;
use Symfony\Component\Finder\Finder;

class ConfigRegister
{
    private const CONFIG_KEY = "config";
    private const CONFIG_PUBLIC_KEY = "public";
    private const CONFIG_PRIVATE_KEY = "private";
    private const PHP_EXTENSION = ".php";
    private const ENV_FILE_NAME = ".env";

    private $root;

    private $extension;

    public static function init() 
    {
        $configRegister = new static();
        $configRegister->root = getcwd();
        $configRegister->extension = self::PHP_EXTENSION;
        return $configRegister;
    }

    public function register()
    {
        // 根据service找到服务路径，在找到服务根配置
        // 找private config 和 public config
        // public 可以覆盖 private
        $this->loadEnv();
        $this->loadService();
        $this->loadConfig();
    }

    private function loadConfig()
    {
        // 从服务配置中获取到私有配置和公有配置路径
        $public = $this->loadPublic();
        $private = $this->loadPrivate();
        Config::setConfig(
            array_merge(
                $public,
                $private
            )
        );
        $config = Config::getConfig();
    }

    private function loadPublic(): array
    {
        // 从服务配置中获取到公有配置
        return $this->load(Config::getService(self::CONFIG_KEY . "." . self::CONFIG_PUBLIC_KEY));
    }

    private function loadPrivate(): array
    {
        // 从服务配置中获取到私有配置
        return $this->load(Config::getService(self::CONFIG_KEY . "." . self::CONFIG_PRIVATE_KEY));
    }

    private function load(string $path): array
    {
        if ($path === NULL) {
            return [];
        }
        
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($path)->name("*" . $this->extension);
        foreach ($finder as $file) {
            $config = [
                $file->getBasename($this->extension) => require($file->getRealPath())
            ];
            if ($file->getRelativePath() != "") {
                $config = [
                    $file->getRelativePath() => $config
                ];
            }
            $configs[] = $config;
        }
        return array_merge(...$configs);
    }

    private function loadService()
    {
        Config::setServiceConfig(
            array_merge_recursive(
                require(
                    $this->getServicePath()
                )
            )
        );
    }

    private function getServicePath()
    {
        $serviceConfigPath =    $this->root .
                            '/' .
                            Application::$service .
                            '/service' .
                            $this->extension;
        if (!file_exists($serviceConfigPath)) {
            throw new ConfigException("Service configuration file not found: " . $serviceConfigPath);
        }
        return $serviceConfigPath;
    }

    private function loadEnv() 
    {
        if (file_exists($this->root . '/' . self::ENV_FILE_NAME)) {
            Dotenv::createMutable($this->root)->load();
        }
    }

}
