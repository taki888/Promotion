<?php

class Mysql
{
	private $dbname;
	public $link;

	function __construct($dbc)
	{
		$this->dbname = $dbc['dbname'];
		$this->link = $this->connect($dbc);
	}

	function __destruct()
	{
		mysql_close($this->link);
	}

	public function free_result($result)
	{
		mysql_free_result($result);
	}

	function connect($dbc)
	{
		if($this->link) return $this->link;
		$link = mysql_connect($dbc['host'] . ':' . $dbc['port'], $dbc['user'], $dbc['password']) or die(mysql_error());
		mysql_select_db($dbc['dbname']) or die(mysql_error());
		mysql_set_charset($dbc['charset']);
		return $link;
	}

	public function query($sql)
	{
		$result = mysql_query($sql, $this->link) or msg(mysql_error());
		return $result;
	}

	public function fetch_assoc($result)
	{
		return mysql_fetch_assoc($result);
	}

	public function fetch_row($result)
	{
		return mysql_fetch_row($result);
	}

	public function fetch_array($result)
	{
		return mysql_fetch_array($result);
	}

	public function insert($table, $arr, $debug = 0, $filter=0)
	{
		if($filter) $arr = $this->filter_fields($table, $arr);
		$fields = "`" . implode("`,`", array_keys($arr)) . "`";
		$vals = "'" . implode("','", array_values($arr)) . "'";
		$sql = "insert into `$table`($fields) values($vals)";
		if ($debug)
		{
			return $sql;
		}
		$this->query($sql);
		$iid = mysql_insert_id();
		return $iid;
	}

	public function update($table, $arr, $cond = '', $debug = 0, $filter=0)
	{
		if($filter) $arr = $this->filter_fields($table, $arr);
		foreach($arr as $k => $v)
		{
			$zdarr[] = "`$k`='$v'";
		}
		$zds = implode(",", $zdarr);
		$cond and $cond = "where $cond";
		$sql = "update `$table` set $zds $cond";
		if ($debug)
		{
			return $sql;
		}
		$this->query($sql);
		$uid = mysql_affected_rows();
		return $uid;
	}


	public function delete($table, $cond, $debug = 0) {
		$sql = "delete from `$table` where $cond";
		if($debug) {
			return $sql;
		}
		$this->query($sql);
	}

	public function filter_fields($table, $arr)
	{
		$zdarr = $this->get_fields($table);
		foreach($arr as $k => $v)
		{
			if (!in_array($k, $zdarr)) unset($arr[$k]);
		}
		return $arr;
	}

	public function get_fields($table) {
		$arr = $this->get_array("desc `$table`");
		return $arr;
	}

	public function affected_rows()
	{
		return mysql_affected_rows();
	}

	public function insert_id()
	{
		return mysql_insert_id();
	}

	public function num_rows($ret)
	{
		$num = mysql_num_rows($ret);
		return $num;
	}

	/**
	 * 返回一行, limit 1的语句
	 */
	public function one($sql)
	{
		$ret = $this->query($sql);
		$row = mysql_fetch_assoc($ret);
		return $row;
	}

	/**
	 * 返回一行中的第一个字段 limit 1的语句
	 */
	public function field($sql)
	{
		$ret = $this->query($sql);
		$row = mysql_fetch_row($ret);
		return $row[0];
	}

	/**
	 * select两个字段，第一个字段作为key，第二个字段作为value
	 * select一个字段，返回的数组下标是自增长
	 */
	public function get_array($sql)
	{
		$ret = $this->query($sql);
		$row = mysql_fetch_row($ret);
		if (count($row) == 2)
		{
			$arr[$row[0]] = $row[1];

			while ($row = mysql_fetch_row($ret))
			{
				$arr[$row[0]] = $row[1];
			}
		}
		else
		{
			$arr[] = $row[0];
			while ($row = mysql_fetch_row($ret))
			{
				$arr[] = $row[0];
			}
		}

		return $arr;
	}

	public function rows($sql, $key = '')
	{
		$a = array();
		$i = 0;
		$ret = $this->query($sql);
		if ($key)
		{
			while ($row = mysql_fetch_assoc($ret))
			{
				$a[$row[$key]] = $row;
			}
		}
		else
		{
			while ($row = mysql_fetch_assoc($ret))
			{
				$a[$i++] = $row;
			}
		}

		return $a;
	}

	public function clean($str) {
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : mysql_real_escape_string($str);
	}

	public function clean1($str) {
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : mysql_real_escape_string(trim($str));
	}

	public function clean2($str) {
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : mysql_real_escape_string(strip_tags($str));
	}

	public function tables($dbname='')
	{
		if(!$dbname) $dbname = $this->dbname;
		$ret = $this->get_array("show tables from $dbname");
		return $ret;
	}

	public function version() {
		return mysql_get_server_info($this->link);
	}

	public function repair($table) {
		$table = $this->clean2($table);
		return $this->one("repair table `$table`");
	}

	public function optimize($table) {
		$table = $this->clean2($table);
		return $this->one("optimize table `$table`");
	}

}

?>