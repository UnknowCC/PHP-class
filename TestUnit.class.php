<?php 


class TestUnit
{
	public static function dump($v, $max_string_length = 100, $max_deep = 5, $max_elements = 10)
	{
		$type = gettype($v);
		switch ($type) {
			case 'NULL':
				return 'NULL';
			case 'boolean':
				return $v === false ? 'FALSE' : 'TRUE';
			case 'integer':
				return ''.(int) $v;
			case 'double':
				$f = (float)$v;
				$s = (string)$v;
				if (is_finite($f) && strpos($s, '.') === false && strpos($s, 'E') === false) {
					$s .= '.0';
				}
				return $s;
			case 'string':
				$s = (string)$v;
				if (strlen($s) > $max_string_length) {
					$s = substr($s, 0, $max_string_length).'[...]';
				}
				return "\"".addcslashes($s, "\000..\037\\\$\"\177..\377")."\"";
			case 'resource':
				return "resource(".get_resource_type($v).")";
			case 'object':
				if (method_exists($v, '__toString')) {
					return $v->__toString();
				} else {
					$a = get_object_vars($v);
					$s = get_class($v).'{';
					if ($max_deep <= 0) {
						return $s.'...}';
					}
					$n = 0;
					foreach ($a as $obj_k => $obj_v) {
						if ($n > 0) {
							$s .= ' ';
						}
						if ($n < $max_elements) {
							$s .= '$'.$obj_k.'='.self::dump($obj_v,$max_string_length,$max_deep-1,$max_elements).';';
						} else {
							$s .= '...';
							break;
						}
						$n++;
					}
					return $s.'}';
				}
			case 'array':
				$s = 'array(';
				if ($max_deep <= 0) {
					return $s.'...)';
				}
				$n = 0;
				foreach ($v as $k => $e) {
					if ($n > 0) {
						$s .= ', ';
					}
					if ($n < $max_elements) {
						$s .= self::dump($k).'=>'.self::dump($e, $max_string_length, $max_deep-1, $max_elements);
					} else {
						$s .= '...';
						break;
					}
					$n++;
				}
				$s .= ')';
				return $s;
			default:
				return 'Unknown type';
		}
	}
}