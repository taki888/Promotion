<?php

/**

导出数据库
$backer = new dbBacki();
$backer-> dbBacki('db.sql');				 备份数据库, 导出文件名为db.sql
导出表
$backer = new dbBacki();
$backer-> backtable('admin','admin.sql');

*/

class dbBacki
{
	public $dbname;
	public $db;
	public $drop;
	public $line = "\n\n-- -----------------------------------------------------------------------\n";

	/**
	 *
	 * @drop : 是否添加drop语句
	 */
	function __construct($dbname, $file = null, $drop = 0)
	{
		global $db;
		$this->db = $db;
		$this->dbname = $dbname;
		$this->drop = $drop;
	}

	function addheader($file)
	{
		$info = "-- 数据库: " . $this->dbname . "\n";
		$info .= "-- 生成日期: " . date("Y-m-d H:i:s", time()) . "\n";
		$info .= "-- 主机: " . mysqli_get_host_info() . "\n";
		$info .= "-- Mysql版本: " . mysqli_get_server_info();
		file_put_contents($file, $info);
	}

	function backdb($file)
	{
		$this->addheader($file);
		$result = $this->db->query("show tables from $this->dbname");
		while ($row = mysqli_fetch_row($result))
		{
			$this->backtable($row[0], $file, 0);
		}
	}


	/**
	 * 每条记录生成一条insert语句，文件较大，导入较慢
	 * 导入时出现MySQL server has gone away错误时，因SQL语句过大或者语句中含有BLOB或者longblob字段, 用该方法取代backtable
	 * max_allowed_packet = 10M(也可以设置自己需要的大小)
	 * max_allowed_packet参数的作用是，用来控制其通信缓冲区的最大长度。
	 */
	function backtable1($table, $file, $header = 1)
	{
		if($header) {
			$this->addheader($file);
		}

		$strut = $this->get_table_structure($table);
		file_put_contents($file, $this->line . $strut, FILE_APPEND);
		$insertsql = "insert into `$table` values";
		$result = $this->db->query("select * from `$table`");
		while ($row = mysqli_fetch_row($result))
		{
			$row = array_map(array($this,'add_quote'), $row);
			$data[] = $insertsql . "('" . implode("','", $row) . "');\n" ;
		}
		mysqli_free_result($result);
		file_put_contents($file, implode("", $data), FILE_APPEND);
	}

	/**
	 * 10记录生成一条insert语句，文件较小，减少执行语句，导入较快
	 * @file : 指定了file名，导出文件重新生成，没指定，内容就追加。
	 */
	function backtable($table, $file, $header = 1)
	{
		if($header) {
			$this->addheader($file);
		}

		$strut = $this->get_table_structure($table);
		file_put_contents($file, $this->line . $strut, FILE_APPEND);
		$insertsql = "insert into `$table` values";
		$data[] = $insertsql;

		$i = -1;
		$result = $this->db->query("select * from `$table`");
		while ($row = mysqli_fetch_assoc($result))
		{
			$i++ ;
			$row = array_map(array($this,'add_quote'), $row);
			if( $i >= 10 ) {
				$data[] = ";\n".$insertsql;
				$i = -1;
			} else {
				$douhao = ($i == 0) ? "" : ",";
				$data[] = "$douhao\n('" . implode("','", $row) . "')";
			}

		}
		mysqli_free_result($result);
		$content = rtrim(implode("", $data), $insertsql);
		file_put_contents($file, $content.";", FILE_APPEND);
	}

	function get_table_structure($table)
	{
		$this->drop and $dropsql = "drop table if exists `$table`;\n";
		$result = $this->db->query("show create table `$table`");
		$row = mysqli_fetch_assoc($result);
		return "\n" . $dropsql . $row['Create Table'] . ";\n\n";
	}

	function add_quote($str)
	{
		return str_replace(array("\\", "'"), array('\\\\', "''"), $str);
	}
}

?>