<?php

/**
 * 数组处理类
 * 主要用于数组的递归获取、增加和删除
 */
class ArrayHandle
{
    /**
     * 从数组中获取键值 可以使用小数点获取嵌套的值
     * @example
     * ArrayHandle::get($_SERVER, 'REQUEST_URI');
     * ArrayHandle::get(array('test' => array('test1' => 'test2')), 'test.test1')
     * @param  array $array
     * @param  string $key      要取值的键名
     * @param  mixed $fallback 获取数组值失败会的回调值
     * @return string
     */
    public static function get($array, $key, $fallback = null)
    {
        foreach ($keys = explode('.', $key) as $key) {
            if (! is_array($array) || ! array_key_exists($key, $array)) {
                return $fallback;
            }
            $array =& $array[$key];
        }
        return $array;
    }

    /**
     * 设置数组中的键值
     * @param string $array 原数组
     * @param string $key   要设置的键名
     * @param mixed $value 需要设置的键值
     */
    public static function set(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! array_key_exists($key, $array)) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
    }

    /**
     * 删除数组中的键值
     * @param  array $array 要操作的数组
     * @param  string $key   要删除的键名
     * @return void
     */
    public static function erase(&$array, $key)
    {
        $keys = explode('.', $keys);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (array_key_exists($key, $array)) {
                $array =& $array[$key];
            }
        }
        if (array_key_exists($key = array_shift($keys), $array)) {
            unset($array[$key]);
        }
    }
}
