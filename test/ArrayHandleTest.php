<?php

require dirname(__DIR__).'/ArrayHandle.class.php';

$array = array('a' => array('b' => array('c' => array('d' => ['f']))));
var_dump(ArrayHandle::get($array, 'a.b.c'));

ArrayHandle::set($array, 'e.h.j', array('apple', 'oriange'));
var_dump($array);
