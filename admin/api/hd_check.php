<?php
require '../conn.php';

//if(!IS_POST) exit('false');

//if(!IS_AJAX) exit('false');
isUserLogin();
session_write_close();

//extract($_POST);
extract($_POST);

$hd_id = intval($hd_id);
$check = intval($check);
$table = "hd_{$hd_id}";
// print_r($_POST);die;
// echo $hd_id."----".$u_id."-----".$check."-----".$table;die;

//查询之前状态
$check_org = $db->one("select is_check from $table where u_id='$u_id'");
$check_org = $check_org['is_check'];
$u_id_arr  = array_values(explode(',', $u_id));
$count     = count($u_id_arr);
if ($hd_id && $u_id) {
    extract($db->clean2($_POST));
    //只有为变更未审核和审核状态才更改redis
    //var_dump($check_org,$check);exit;
    if ($check == '1' || $check == '2') {
        if ($check_org != '1' && $check_org != '2') {
            $redis->hincrby('hd_feifan', $hd_id, -$count);
        }
    } else if ($check == '0') {
        $redis->hincrby('hd_feifan', $hd_id, +$count);
    }
    //小于1时设置为0
    if ($redis->hget('hd_feifan', $hd_id) < 1) {
        $redis->hset('hd_feifan', $hd_id, 0);
    }
    //更新状态
    $pdata = array(
        'msg'        => $msg,
        'check_time' => time(),
    );
    //值为3时修改留言，不写入数据库
    if ($check != '3') {
        $pdata = array_merge($pdata, ['is_check' => $check]);
    }
    foreach ($u_id_arr as $key => $value) {
        $db->update($table, $pdata, "u_id=" . $value);
    }
} else {
    $json['error'] = 1;
    $json['msg']   = '参数错误';
    //echo '参数错误';
    exit(json_encode($json));
}
if ($check != '0' && $check != '3') {
    $hd_name = $db->field("select hd_name from huodong where hd_id=$hd_id limit 1");
    $u_name1 = mb_substr($_POST['u_name'], 0, 3, 'utf8') . "***";
    //$u_name1 = mb_substr($_POST['u_name'],0,3,'utf-8')."***";
    $redis->lpush("user_gd_feifan", "{$u_name1}成功办理{$hd_name}");
    $ok_num = $redis->get("ok_num_feifan");
    $ok_num = $ok_num ? $ok_num : 50;
    $redis->ltrim("user_gd_feifan", 0, $ok_num);
}
foreach ($u_id_arr as $key => $value) {
    $data = [
        'check_name' => $_SESSION['u_name'],
        'u_ip'       => $_SESSION['u_ip'],
        'time'       => time(),
        'hd_id'      => $hd_id,
        'u_id'       => $value,
        'u_name'     => $_POST['u_name'],
    ];
    if ($check != '3') {
        //值为3时修改留言，不写入数据库
        $data = array_merge($data, ['type' => $check]);
    }
    $db->insert('check', $data);
}
$json['error'] = 0;
$json['msg']   = '操作成功';
echo json_encode($json);

logger("审核活动,id:{$hd_id}");
