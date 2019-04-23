<?php

class Mysqli2
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
		$this->link->close();
	}

	public function free_result($result)
	{
		$result->free();
	}

	protected function connect($dbc)
	{
		if($this->link) return $this->link;
		$link =  new mysqli($dbc['host'], $dbc['user'], $dbc['password'], $dbc['dbname'], $dbc['port']) or die(mysqli_connect_error());
		//$link =  new PDO("mysql:host=localhost;dbname=$dbc['dbname']","$dbc['user']","$dbc['password']","array(PDO::ATTR_PERSISTENT => true)");
		$link->set_charset($dbc['charset']);
		return $link;
	}

	public function query($sql)
	{
		//var_dump($this->link);
		$result = $this->link->query($sql) or msg($this->link->error);
		//$result = $this->link->query($sql);
		//var_dump($sql);
		return $result;
	}

	public function multi_query($sql)
	{
		$result = $this->link->multi_query($sql) or msg($this->link->error);
		return $result;
	}

	public function fetch_assoc($result)
	{
		return $result->fetch_assoc();
	}

	public function fetch_row($result)
	{
		return $result->fetch_row();
	}

	public function fetch_array($result)
	{
		return $result->fetch_array();
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
		$iid = $this->link->insert_id;
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
		$uid = $this->link->affected_rows;
		return $uid;
	}

	public function delete($table, $cond, $debug = 0) {
		$sql = "delete from `$table` where $cond";
		if($debug) {
			return $sql;
		}
		$this->query($sql);
		$uid = $this->link->affected_rows;
		return $uid;
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
		return $this->link->affected_rows();
	}

	public function insert_id()
	{
		return $this->link->insert_id();
	}

	public function num_rows($ret)
	{
		$num = mysqli_num_rows($ret);
		return $num;
	}

	/**
	 * 返回一行, limit 1的语句
	 */
	public function one($sql)
	{
		$ret = $this->query($sql);
		$row = mysqli_fetch_assoc($ret);
		return $row;
	}

	/**
	 * 返回第一个字段 limit 1的语句
	 */
	public function field($sql)
	{
		$ret = $this->query($sql);
		$row = mysqli_fetch_row($ret);
		return $row[0];
	}

	/**
	 * select两个字段，第一个字段作为key，第二个字段作为value
	 * select一个字段，返回的数组下标是自增长
	 */
	public function get_array($sql)
	{
		$ret = $this->query($sql);
		$row = mysqli_fetch_row($ret);
		if (count($row) == 2)
		{
			$arr[$row[0]] = $row[1];

			while ($row = mysqli_fetch_row($ret))
			{
				$arr[$row[0]] = $row[1];
			}
		}
		else
		{
			$arr[] = $row[0];
			while ($row = mysqli_fetch_row($ret))
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
			while ($row = mysqli_fetch_assoc($ret))
			{
				$a[$row[$key]] = $row;
			}
		}
		else
		{
			while ($row = mysqli_fetch_assoc($ret))
			{
				$a[$i++] = $row;
			}
		}

		return $a;
	}

	public function clean($str)
	{
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : $this->link->real_escape_string($str);
		//return $str;
	}

	public function clean1($str)
	{
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : $this->link->real_escape_string(trim($str));
	}

	public function clean2($str)
	{
		return (is_array($str)) ? array_map(array($this, __FUNCTION__), $str) : $this->link->real_escape_string(strip_tags(trim($str)));
	}

	public function tables($dbname = '')
	{
		if (!$dbname) $dbname = $this->dbname;
		$arr = $this->get_array("show tables from $dbname");
		return $arr;
	}

	public function version()
	{
		return $this->link->get_server_info();
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