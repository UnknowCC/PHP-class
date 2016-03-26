<?php

require dirname(__DIR__).'/Validator.class.php';

$payload = array('name' => 'abclokjugthg', 'password' => 'testab');
$validator = new ValidatorForm($payload);

$validator->check('name')->is_max(7, '姓名至少需要7个字符');
$validator->check('password')->is_max(7, '密码至少需要7个字符');
if ($errors = $validator->errors()) {
    var_dump($errors); // 密码至少需要7个字符
} else {
    // do nothing
}
