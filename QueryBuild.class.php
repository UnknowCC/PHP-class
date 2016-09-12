<?php 

/**
* SQL联表查询语句拼接、封装
*/
class QueryHelper
{
	/**
	 * 将数组拼接成字符串，如果为索引键名，则将键名作为每个键值的前缀拼接 [key1 => [val1, val2, ..]]  
	 * 结果 key1.val1,key1.val2,...
	 * 当前主要为SQL联表查询的语句拼接用  
	 * @param array $array 
	 * @return string 
	 */
	public static function arrayToStringForFields(array $fieldArray)
	{
		$returnString = '';
		foreach ($fieldArray as $table => $fields) {
			if (!is_array($fields)) {
				$fields = (array) $fields;
			}
			foreach ($fields as $field) {
				$returnString .= (is_numeric($key) ? '' : $key.'.').$value.',';
			}
		}

		return trim($returnString, ',');
	}

	/**
	 * 将数组拼接成字符串，如果为索引键名，则将键名作为每个键值的前缀拼接 [field1 => [table1, table2], [field2 => [table1, table3]]]  
	 * 结果 table1.field1=table2.field1,table1.field2=table3.field2
	 * 当前主要为SQL联表查询的ON条件拼接用  
	 * @param array $array 
	 * @return string 
	 */
	public static function arrayToStringOn(array $onArray)
	{
		$returnString = '';
		foreach ($onArray as $ons) {
			if (!is_array($value) || count($value) != 2) {
				// 有不符合两表字段比较格式的
				return '';
			}
			$onStr = '';
			foreach ($ons as $table => $on) {
				$onStr .= $table.'.'.$on.'=';
			}
			$returnString .= trim($onStr, '=').',';
		}

		return trim($returnString, ',');
	}

	/**
	 * 联表查询封装函数
	 * @param  array   $field     各表查询的字段，表明作为键名
	 * @param  array   $on        各表的链接关系
	 * @param  array   $condition 查询条件
	 * @param  array   $join      表连接方式，默认为left
	 * @param  string  $order     排序方式
	 * @param  string  $limit     返回数量
	 * @param  boolean $find      find or select, 默认select
	 * @example  
	 *      $field = [
	            'cart' => ['cart_id', 'goods_id', 'goods_name', 'store_id', 'goods_image', 'goods_num', 'goods_price'],
	            'goods' => ['goods_state', 'goods_promotion_type', 'goods_storage'],
	            'goods_common' => ['goods_tallage', 'entrepot_type', 'entrepot_money']
	        ];

	        $on = [
	            [
	                'cart' => 'goods_id',
	                'goods' => 'goods_id'
	            ],
	            [
	                'goods' => 'goods_commonid',
	                'goods_common' => 'goods_commonid'
	            ]
	        ];

	        $join = ['inner', 'left'];
	 *
	 *
	 * 
	 * @return array             
	 */
	public function multiTableQuery(array $field, array $on, array $condition, array $join = [], $order = '', $findOne = false, $limit = '')
	{
		$tableStr = implode(',', array_keys($field));
		$fieldStr = static::arrayToStringForFields($field);
		$onStr = static::arrayToStringOn($on);
		$count = count($field);
		$joinCount = count($join);
		if (empty($join) || $joinCount < ($count - 1)) {
			array_push($join, array_fill(0, $count - $joinCount - 1, 'left'));
		} elseif ($joinCount > ($count - 1)) {
			$join = array_slice($join, 0, $count - 1);
		}
		$joinStr = implode(',', $join);

		$model = Model();
		$model->table($tableStr)->field($fieldStr)->join($joinStr)->on($onStr)->where($condition)->order($order)->limit($limit);
		if ($findOne) {
			$result = $model->find();
		} else {
			$result = $model->select();
		}
		return $result;
	}
}
