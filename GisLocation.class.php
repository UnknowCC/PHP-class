<?php 

/**
* 坐标辅助类
*/
class GisLocation
{
	/**
	 * 判断方法
	 * @param  float  $x  坐标经度
	 * @param  float  $y  坐标纬度
	 * @param  array   $ps 多边形区域
	 * @return boolean     
	 */
	public static function isPtInPoly($x, $y, array $ps)
	{
		$count = count($ps);
		$sum = 0;

		for ($i=0; $i < $count; $i++) {
			$x1 = $ps[$i]['x'];
			$y1 = $ps[$i]['y'];
			if ($i == $count - 1) {
				$x2 = $ps[0]['x'];
				$y2 = $ps[0]['y'];
			} else {
				$x2 = $ps[$i+1]['x'];
				$y2 = $ps[$i+1]['y'];
			}
			// 先判断坐标是否在两个端点的水平平行线之间，有则可能有交点
			if ((($y >= $y1) && ($y < $y2)) || (($y >= $y2) && ($y < $y1))) {
				if (abs($y1 - $y2) > 0) {
					// 求出坐标向左射线与边的交点的x坐标
					$dLon = $x1 - (($x1 - $x2) * ($y1 - $y)) / ($y1 - $y2);
					// 如果交点在A点左侧，则有交点
					if ($dLon < $x) {
						$sum++;
					}
				}
			}
		}
		if (($sum % 2) != 0) {
			return true;
		}

		return (($sum % 2) ? true : false);
	}

	/**
	 * 地球半径(km)
	 * @var float
	 */
	public static $earchRadius = 6371.0;

	/**
	 * 根据Haversine公式求圆形两点间的距离
	 * 公式原型
	 * 		haversin(d/R) = haversin($lat2 - $lat1) + cos($lat1) * cos($lat2) 
	 * 			* haversin($lon2 - $lon1)
	 * 		haversin($) = pow(sin($ / 2), 2) = (1 - cos($)) / 2
	 * @param  float $lat1 
	 * @param  float $lon1 
	 * @param  float $lat2 
	 * @param  float $lon2 
	 * @return float       
	 */
	public static function distance($lat1, $lon1, $lat2, $lon2)
	{
		$lat1 = static::convertDegreesToRadians($lat1);
		$lon1 = static::convertDegreesToRadians($lon1);
		$lat2 = static::convertDegreesToRadians($lat2);
		$lon2 = static::convertDegreesToRadians($lon2);

		$diffLat = abs($lat1 - $lat2);
		$diffLon = abs($lon1 - $lon2);

		$h = static::haverSin($diffLat) + cos($lat1) * cos($lat2) * static::haverSin($diffLon);
		$distance = 2 * static::$earchRadius * asin(sqrt($h));
		return $distance;
	}

	/**
	 * Haversine
	 * @param  float $theta 
	 * @return float        
	 */
	public static function haverSin($theta)
	{
		$hs = sin($theta);
		return $hs * $hs;
	}

	/**
	 * 将角度换算成弧度
	 * @param  float $degrees 
	 * @return float          
	 */
	public static function convertDegreesToRadians($degrees)
	{
		return $degrees * pi() / 180;
	}

	/**
	 * 将弧度换算成角度
	 * @param  float $radians 
	 * @return float          
	 */
	public static function convertRadiansToDegrees($radians)
	{
		return $radians * 180.0 / pi();
	}
}

/*

示例坐标点

$ps = array(
	array('x' => 120.2043, 'y' => 30.2795),
	array('x' => 120.2030, 'y' => 30.2511),
	array('x' => 120.1810, 'y' => 30.2543),
	array('x' => 120.1798, 'y' => 30.2781),
	array('x' => 120.1926, 'y' => 30.2752),
);
// $x = 120.1936;
// $y = 30.2846;
// $x = 120.1823;
// $y = 30.2863;
// $x = 120.2189;
// $y = 30.2712;
// $x = 120.1902;
// $y = 30.2712;
// $x = 120.1866;
// $y = 30.2572;
$x = 120.1866;
$y = 30.2718;
$result = GisTest::isPtInPoly($x, $y, $ps);
var_dump($result);

*/

// echo GisLocation::distance(120.2043,30.2795,120.2043,30.2796);