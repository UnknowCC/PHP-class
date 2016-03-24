<?php

/**
 * 自动加载类
 */
class Autoload
{
    /**
     * 定义类的后缀名
     */
    const EXT = '.class.php';

    /**
     * 加载的路径
     * @var array
     */
    public static $directories = array();

    /**
     * 类的别名
     * @example Autoload::$aliases = $array();
     * @var array
     */
    public static $aliases = array();

    /**
     * 添加加载类的路径
     * @example Autoload::directory($pathname);
     * @param  string|array $paths 路径地址
     * @return static
     */
    public static function directory($paths)
    {
        if (! is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            static::$directories[] = rtrim($path, '/').DIRECTORY_SEPARATOR;
        }
    }

    /**
     * 加载类的方法
     * @param  string $class 类名
     * @return mixed
     */
    public static function load($class)
    {
        $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, ltrim($class, '\\'));
        $lowerFile = strtolower($file);

        if (array_key_exists(strtolower($class), array_change_key_case(static::$aliases))) {
            return class_alias(static::$aliases[$class], $class);
        }

        foreach (static::$directories as $directory) {
            if (is_readable($path = $directory.$lowerFile.self::EXT)) {
                return require $path;
            } elseif (is_readable($path = $directory.$file.self::EXT)) {
                return require $path;
            }
        }

        return false;
    }
}

spl_autoload_register("Autoload::load");

Autoload::directory(__DIR__);

$test = new ArrayHandle();
