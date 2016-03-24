<?php


use Exception;
/**
 * 字段验证
 */
class validator
{
    /**
     * 需要验证的数据
     * @var array
     */
    private $payload = array();
    /**
     * 需要验证的字段名
     * @var string
     */
    private $key;
    /**
     * 需要验证的字段值
     * @var mixed
     */
    private $value;
    /**
     * 验证产生的错误信息
     * @var array
     */
    private $errors = array();
    /**
     * 验证的方法
     * @var array
     */
    private $methods = array();

    /**
     * 架构函数
     * @param array $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
        $this->validatorMethod();
    }

    /**
     * 默认的验证方法
     * @access private
     */
    private function validatorMethod()
    {
        $this->methods['null'] = function ($str) {
            return is_null($str);
        };

        $this->methods['min'] = function ($str, $length) {
            return strlen($str) <= $length;
        };

        $this->methods['max'] = function ($str, $length) {
            return strlen($str) >= $length;
        };


        $this->methods['float'] = function ($str) {
            return is_float($str);
        };

        $this->methods['int'] = function ($str) {
            return is_int($str);
        };

        $this->methods['url'] = function ($str) {
            return filter_var($str, FILTER_VALIDATE_URL) !== false;
        };

        $this->methods['email'] = function ($str) {
            return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
        };

        $this->methods['ip'] = function ($str) {
            return filter_var($str, FILTER_VALIDATE_IP) !== false;
        };

        $this->methods['alnum'] = function ($str) {
            return ctype_alnum($str);
        };

        $this->methods['contains'] = function ($str, $needle) {
            return strpos($str, $needle) !== false;
        };

        $this->methods['regex'] = function ($str, $pattern) {
            return preg_match($pattern, $str);
        };
    }

    /**
     * 添加的回调验证方法
     * @param string  $method
     * @param Closure $callback
     */
    public function add($method,Closure $callback)
    {
        $this->methods[$method] = $callback;
    }

    /**
     * 输入的数据中是否存在待验证的字段
     * @param  string $key 字段名
     * @return $this
     */
    public function check($key)
    {
        unset($this->key, $this->value);
        if (key_exists($key, $this->payload)) {
            $this->key = $key;
            $this->value = $this->payload[$key];
        }
        return $this;
    }

    /**
     * 添加验证方法
     * @param  string $method 验证方法
     * @param  array $params 待验证的字段值
     * @return $this
     */
    public function __call($method, $params)
    {
        if (is_null($this->key)) {
            return $this;
        }

        if (strpos($method, 'is_') === 0) {
            $method = substr($method, 3);
            $reverse = false;
        } elseif (strpos($method, 'not_') === 0) {
            $method = substr($method, 4);
            $reverse = true;
        }

        if (!(isset($this->methods[$method]) && $this->methods[$method] instanceof Closure)) {
            throw new Exception('Validator method '.$method.' not found');
        }

        $validator = $this->methods[$method];

        $message = array_pop($params);

        $result = (bool) call_user_func_array($validator, array_merge(array($this->value), $params));

        $result = (bool) ($result ^ $reverse);

        if ($result === false) {
            $this->errors[$this->key][] = $message;
        }

        return $this;
    }

    /**
     * 获取验证的错误信息
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}
