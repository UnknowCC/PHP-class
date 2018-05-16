<?php 


/**
* ip相关函数
*/
class IpTransform
{
	public static $defaultCity = 'UNKNOWN';

	public static function detectCity($ip)
	{
		// mock a agent
		$useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0';

		$thirdIpUrl = 'http://ipinfodb.com/ip_locator.php?ip='.urlencode($ip);
		echo $thirdIpUrl;exit;
		$ch = curl_init();
		$curl_opt = array(
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_USERAGENT => $useragent,
			CURLOPT_URL => $thirdIpUrl,
			CURLOPT_TIMEOUT => 30,
			// CURLOPT_REFERER => 'http://'.$_SERVER['HTTP_HOST'],
		);

		curl_setopt_array($ch, $curl_opt);
		$content = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		var_dump($content);
	}
}

IpTransform::detectCity('117.25.63.99');