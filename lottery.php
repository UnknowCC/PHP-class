<?php 
/**
* 概率抽奖
*/
class Lottery
{

	/**
	 * 抽
	 * @param  array  $prob 抽奖概率分布的数组
	 * @example [1 => 1%, 2 => 3%, 3 => 7%, 4 => 20%, 5 => 69%] 
	 *          输入的数组应为 [1, 3, 7, 20, 69]
	 * @return int|string       
	 */
	public static function draw(array $prob)
	{
		$total = array_sum($prob);
		$temp = range(1, $total);
		shuffle($temp);
		$rand = mt_rand(1, $total);
		$index = array_search($rand, $temp);
		
		$t = 0;
		foreach ($prob as $key => $value) {
			$t += $value;
			if ($index <= $t) {
				return $key;
			}
			continue;
		}
	}
}


/* Example */

/*$lott = array(
	'一等奖' => 1,
	'二等奖' => 5,
	'三等奖' => 20,
	'四等奖' => 60,
	'安慰奖' => 500
);
echo Lottery::draw($lott);
*/