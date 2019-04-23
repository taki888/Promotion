<?php

require('../conn.php');

if(!IS_AJAX) exit('false');

isUserLogin(array(1));
session_write_close();

if(!$_GET['id']) {
	die();
}

delRow();

$json['error'] = 0;
$json['msg'] = '';

echo json_encode($json);

function delRow()
{
	global $db;
	$id = $_GET['id'];

	$u_nums = $db->delete("info", "id = $id");
	logger("删除用户,id:{$ids}");
	return $u_nums;

}

?>