<?php 

/**
 * For safe multipart POST request for PHP5.3 ~ PHP5.4
 * @param  resource $ch    cURL resource
 * @param  array  $assoc "name => value"
 * @param  array  $files "name => path"
 * @return bool        
 */
function curl_custom_postfields($ch, array $assoc = array(), array $files = array())
{
	static $disallow = array("\0", "\"", "\r", "\n");

	foreach ($assoc as $k => $v) {
		$k = str_replace($disallow, "_", $k);
		$body[] = implode("\r\n", array(
			"Content-Disposition: form-data; name=\"{$k}\"",
			"",
			filter_var($v),
		));
	}

	foreach ($files as $k => $v) {
		switch (true) {
			case false === $v = realpath(filter_var($v)):
			case !is_file($v):
			case !is_readable($v):
				continue;
		}
		$data = file_get_contents($v);
		$v = call_user_func('end', explode(DIRECTORY_SEPARATOR, $v));
		$k = str_replace($disallow, "_", $k);
		$v = str_replace($disallow, "_", $v);
		$body = implode("\r\n", array(
			"Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
			"Content-Type: application/octet-stream",
			"",
			$data,
		));
	}

	do {
		$boundary = "------------------".md5(mt_rand().microtime());
	} while (preg_grep("/{$boundary}/", $body));

	array_walk($body, function (&$part) use ($boundary) {
		$part = "--{$boundary}\r\n{$part}";
	});

	$body[] = "--{$boundary}--";
	$body[] = "";

	return @curl_setopt_array($ch, array(
		CURLOPT_POST  => true,
		CURLOPT_POSTFIELDS  => implode("\r\n", $body),
		CURLOPT_HTTPHEADER  => array(
			"Expect: 100-continue",
			"Content-Type: multipart/form-data: boundary={$boundary}",
		),
	));
}