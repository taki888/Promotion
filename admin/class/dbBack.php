<?php

/**

几个表要导出到同一个文件时，new dbBack时指定文件名, backtable()时不指定文件名。
一表一文件时，new dbBack时不指定文件名，backtable()指定文件名

导出数据库
$backer = new dbBack($dbc['dbname']);         备份数据库时，第二个参数必需要指定。备份表时为可选
$backer-> dbBack('db.sql');										 备份数据库, 导出文件名为db.sql
导出表
$backer = new dbBack($dbc['dbname']);
$backer-> backtable('admin','admin.sql');

*/

class dbBack
{
	public $dbname;
	public $drop;
	public $line = "\n\n-- -----------------------------------------------------------------------\n";

	/**
	 *
	 * @drop : 是否添加drop语句
	 */
	function __construct($dbname, $drop = 0)
	{
		$this->dbname = $dbname;
		$this->drop = $drop;
	}

	function addheader($file)
	{
		$info = "-- 数据库: " . $this->dbname . "\n";
		$info .= "-- 生成日期: " . date("Y-m-d H:i:s", time()) . "\n";
		$info .= "-- 主机: " . mysql_get_host_info() . "\n";
		$info .= "-- Mysql版本: " . mysql_get_server_info();
		file_put_contents($file, $info);
	}

	function backdb($file)
	{
		$this->addheader($file);
		$result = mysql_query("show tables from $this->dbname");
		while ($row = mysql_fetch_row($result))
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
		$result = mysql_query("select * from `$table`");
		while ($row = mysql_fetch_row($result))
		{
			$row = array_map('mysql_real_escape_string', $row);
			$data[] = $insertsql . "('" . implode("','", $row) . "');\n" ;
		}
		mysql_free_result($result);
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
		$result = mysql_query("select * from `$table`");
		while ($row = mysql_fetch_assoc($result))
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
		mysql_free_result($result);
		$content = rtrim(implode("", $data), $insertsql);
		file_put_contents($file, $content.";", FILE_APPEND);
	}

	function get_table_structure($table)
	{
		$this->drop and $dropsql = "drop table if exists `$table`;\n";
		$result = mysql_query("show create table `$table`");
		$row = mysql_fetch_assoc($result);
		return "\n" . $dropsql . $row['Create Table'] . ";\n\n";
	}

	function add_quote($str)
	{
		return str_replace(array("\\", "'"), array('\\\\', "''"), $str);
	}
}

?>