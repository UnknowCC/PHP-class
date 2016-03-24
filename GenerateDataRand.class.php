<?php

/**
 * 将指定数值分解成指定范围内的随机数字，最终生成的数字全在指定范围内
 * 用法：
 * $gene = new DataRand(20000, 1950, 2050);
 * $arr = $gene->generatorData();
 */
class DataRand
{
    public $number;
    public $randMin;
    public $randMax;

    public function __construct($number, $randMin, $randMax)
    {
        $this->number = $number;
        $this->randMin = $randMin;
        $this->randMax = $randMax;
    }

    public function generateRandData()
    {
        less:$rands = array();
        $num = $this->number;
        while ( $num > $this->randMax) {
            $rand = rand( $this->randMin, $this->randMax);
            $num -= $rand;
            $rands[] = $rand;
            if ($num <= $this->randMin) {
                goto less;
            }
        }
        $rands[] = $num;
        return $rands;
    }
}


/**
 * 函数方法
 * @example generateRandData(20000, 1950, 2050)
 * @param  int $num     要分解的数字
 * @param  int $randmin 最新随机数
 * @param  int $randmax 最大随机数
 * @return array          生成的随机数组
 */
function generateRandData($num, $randmin, $randmax)
{
    less:$rands = array();
    $tNum = $num;
    while ($tNum > $randmax) {
        $rand = rand($randmin, $randmax);
        $tNum -= $rand;
        $rands[] = $rand;
        if ($tNum < $randmin) {
            goto less;
        }
    }
    $rands[] = $tNum;
    return $rands;
}
