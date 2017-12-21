<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 数据库条件工具类
 */

class DbConditionUtil
{
	/**
	 * 能接收的连接条件符号
	 */
	private $symbol = array(
		'=',
		'<>',
		'<',
		'<=',
		'>',
		'>=',
		'like',
		'in',
	);

	/**
	 * 条件
	 */
	private $_condition = array();

	/**
	 * 兼容方法，以前的都是传的数组或字符串
	 * ---
	 * @param array|string $conditions 条件
	 * @return void
	 */
	public function whereArray($conditions)
	{
		if ( ! $conditions) {
			return ;
		}

		if ( ! is_array($conditions)) {
			$this->_condition[] = $conditions;
			return ;
		}

		if ($conditions) foreach ($conditions as $k => $v) {
			$symbol = '';
			if (strpos($k, ':') !== false) {
				list($key, $symbol) = explode(':', $k);
			} else {
				$key = $k;
			}
			$symbol = $symbol ? $symbol : '=';
			$this->where($key, $symbol, $v);
		}
	}

	/**
	 * 设置条件方法
	 * ---
	 * @param string $key 字段名称
	 * @param string $symbol 连接条件
	 * @param mixed $value 字段值
	 */
	public function where($key, $symbol, $value)
	{
		if ( ! in_array($symbol, $this->symbol)) {
			return false;
		}

		switch ($symbol) {
			case '=':
				$this->_equal($key, $value);
				break;
			case '<>':
				$this->_notEqual($key, $value);
				break;
			case '<':
				$this->_lessThan($key, $value);
				break;
			case '<=':
				$this->_lessThanEqual($key, $value);
				break;
			case '>':
				$this->_moreThan($key, $value);
				break;
			case '>=':
				$this->_moreThanEqual($key, $value);
				break;
			case 'like':
				$this->_like($key, $value);
				break;
			case 'in':
				$this->_in($key, $value);
				break;
		}
	}

	/**
	 * 等于 =
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _equal($key, $value)
	{
		$this->_condition[] = "`{$key}` = '" . $this->_escape($value) . "'";
	}

	/**
	 * 不等于 <>
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _notEqual($key, $value)
	{
		$this->_condition[] = "`{$key}` <> '" . $this->_escape($value) . "'";
	}

	/**
	 * 小于 <
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _lessThan($key, $value)
	{
		$this->_condition[] = "`{$key}` < '" . $this->_escape($value) . "'";
	}

	/**
	 * 小于 <=
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _lessThanEqual($key, $value)
	{
		$this->_condition[] = "`{$key}` <= '" . $this->_escape($value) . "'";
	}

	/**
	 * 大于 >
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _moreThan($key, $value)
	{
		$this->_condition[] = "`{$key}` > '" . $this->_escape($value) . "'";
	}

	/**
	 * 大于 >=
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _moreThanEqual($key, $value)
	{
		$this->_condition[] = "`{$key}` >= '" . $this->_escape($value) . "'";
	}

	/**
	 * 模糊 like
	 * ---
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	private function _like($key, $value)
	{
		$this->_condition[] = "`{$key}` like '" . $this->_escape($value) . "'";
	}

	/**
	 * 包括 in
	 * ---
	 * @param string $key 键
	 * @param array $value 值
	 * @return void
	 */
	private function _in($key, $value)
	{
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->_escape($v);
			}
			$value = implode(',', $value);
		}
		$this->_condition[] = "`{$key}` in (" . $value . ")";
	}

	/**
	 * 获取条件结果
	 * ---
	 * @return string
	 */
	public function getConditions()
	{
		// 拼接条件
		$result = '';
		if ($this->_condition) {
			$result = implode(' and ', $this->_condition);
			$this->_condition = array();
		}

		return $result;
	}

	/**
	 * 转义查询条件
	 * ---
	 * @param string $value 字段值
	 * @return mixed $result 转义结果
	 */
	private function _escape($value)
	{
		return addslashes($value);
	}
}
