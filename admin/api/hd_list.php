<?php

require '../conn.php';

if (!IS_AJAX) {
    exit('false');
}

// isUserLogin();
// session_write_close();

// $scon = "where 1 ";

// $hd_status = intval($_GET['hd_status']);
// $hd_name = $db->clean2($_GET['hd_name']);

// if ($hd_name)
// {
//     $hd_name = str_replace(array('%', '_'), array('\%', '\_'), $hd_name);
//     $cond1 = "and hd_name like '%$hd_name%' ";
// }

// if( strlen($_GET['hd_status']) )
// {
//     $cond2 = "and hd_status='$hd_status' ";
// }

// $cond = $scon . $cond1 . $cond2;

//echo $cond;

$pagesize = $_GET['pagesize'] ? intval($_GET['pagesize']) : 30;
$pagesize = min($pagesize, 100);

$pager = limiter($pagesize);
$num   = $db->field("select count(*) from info");

$data['page_count'] = ceil($num / $pagesize);

$result = $db->query("select * from info order by id asc");
while ($row = $db->fetch_assoc($result)) {
    extract($row);
    $row['hd_zd_vals'] = unserialize($hd_zd_vals);
    $rows[]            = $row;
}
//刷新时间
$time = $db->one("select time from update_time where id=1");
$time = $time['time'];

$json['error'] = 0;
$json['msg']   = '';

$data['list'] = $rows;
$data['time'] = $time;
$json['data'] = $data;

//printr($rows);

echo json_encode($json);
