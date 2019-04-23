<?php

require('../conn.php');

if(!IS_AJAX) exit('false');

isUserLogin();
session_write_close();

$id = intval($_GET['id']);    // 活动id

if(!$id) {
	die();
}

//时间参数
$begin_time=empty($_GET['begin_time'])?'':$_GET['begin_time'];
$over_time=empty($_GET['over_time'])?'':$_GET['over_time'];

$json = array();

$scon = "where 1 ";

if(!empty($begin_time) && empty($over_time)){
	$begin_time=strtotime($begin_time);
	$scon.=" and post_time > $begin_time";
}
if(!empty($over_time) && empty($begin_time)){
	$over_time=strtotime($over_time);
	$scon.=" and post_time < $over_time";
}
if(!empty($begin_time) && !empty($over_time)){
	$begin_time=strtotime($begin_time);
	$over_time=strtotime($over_time);
	$scon.=" and post_time > $begin_time and post_time < $over_time";
}

$check = intval($_GET['check']);
$uname = $db->clean2($_GET['uname']);

if ($uname)
{
	$uname = str_replace(array('%', '_'), array('\%', '\_'), $uname);
	$cond1 = " and u_name like '%$uname%' ";
}

if( strlen($_GET['check']) )
{
	$cond2 = " and is_check='$check' ";
}

$cond = $scon . $cond1 . $cond2;
// exit($cond);

$table = "hd_$id";

$pagesize = $_GET['pagesize'] ? intval($_GET['pagesize']) : 30;
$pagesize = min($pagesize, 100);

$pager = limiter($pagesize);
$num = $db->field("select count(*) from $table $cond");

$data['page_count'] = ceil($num/$pagesize);

$rows = $db->rows("select * from $table $cond order by u_id desc $pager");

$json['error'] = 0;
$json['msg'] = '';

$data['list'] = $rows;
$data['num'] = $num;
$json['data'] = $data;

//printr($rows);

echo json_encode($json);

?>