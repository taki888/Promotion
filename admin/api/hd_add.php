<?php

require_once '../aliyun-oss/Common.php';
require('../conn.php');
require('../fun.php');

if(!IS_AJAX) exit('false');

isUserLogin();
session_write_close();

$hd_id = intval($_POST['hd_id']);

extract($_POST);


if($img64) {
    $img_path = str64_to_img($img64, '.');
//  exit($img_path);
//  $g = getimagesize($tmpName);
//  if ($g[0] > 500)  resetImg($img_path, 500, 0);
    $oss_logo_path = img2oss(dirname(__DIR__)."/img/".$img_path, basename($img_path));

//  echo $img_path,'<br />';
//  echo $oss_logo_path,'<br />';
//  die();

}

$arr2 = formater($hd_zds);
$hd_zd_names = $arr2['zd_name'];
$hd_zd_types = $arr2['zd_type'];
$hd_zd_vals = $arr2['zd_val'];
$hd_zd_pys = get_pys($hd_zd_names);


if ($hd_zd_names && count($hd_zd_names) != count(array_unique($hd_zd_names))) {
   json_error('字段名有重复');
}

if ($_POST['hd_name']) {
    extract($db->clean($_POST));
    $hd_zd_pys = get_pys($hd_zd_names);
    $hd_time1 = $hd_time1 != 'null' ? $hd_time1 : '0';
    $hd_time2 = $hd_time2 != 'null' ? $hd_time2 : '2027-01-15 16:00:00';
	//默认时间限制
	if($hd_time_limit === ""){
        $hd_time_limit=(string)(60*12);
    }else{
        $hd_time_limit=(int)$hd_time_limit;
    }
	$hd_time_limit=(string)$hd_time_limit;
    //限制长度
    $hd_time_limit=mb_substr($hd_time_limit,0,5,'utf-8');
    
	/*	bug
	//申请更新时间
    if($update_time !== ""){
        $time_retrun=$db->insert('update_time',['time'=>$update_time]);
        if(!$time_retrun){
			$json['time'] = '刷新时间('.$update_time.')重定义失败';
            //exit(json_error());
        }
    }else{
        $update_time='000000';
        $time_retrun=$db->insert('update_time',['time'=>$update_time]);
        if(!$time_retrun){
			$json['time'] = '刷新时间(000000)重定义失败';
			//exit(json_error('刷新时间(000000)重定义失败'));
        }
    }*/
	
	//活动是否更新,默认关闭
	//if($hd_time_update !== '0' && $hd_time_update !== '1'){
	//	$hd_time_update='0';
	//}


    $pdata = array(
        'hd_name'=> $hd_name,
        'hd_time1'=> strtotime($hd_time1),
        'hd_time2'=> strtotime($hd_time2),
        'hd_status'=> $hd_status,
        'hd_intro'=> $hd_intro,
        'hd_rules'=> $hd_rules,
        'hd_logo'=> $oss_logo_path,
        'hd_time'=> time(),
        'hd_index'=> $hd_index,
        'hd_zd_vals'=> $db->clean(serialize($hd_zd_vals)),
		'hd_time_limit'=>$hd_time_limit,
		//'hd_time_update'=>$hd_time_update,

        'hd_zd_names'=> $hd_zd_names ? implode(',', $db->clean2($hd_zd_names)) : "",
        'hd_zd_pys'=> $hd_zd_pys ? implode(',', $db->clean2($hd_zd_pys)) : "",
        'hd_zd_types'=> $hd_zd_types ? implode(',', $db->clean2($hd_zd_types)) : "",
    );
	
    if(!$oss_logo_path) unset($pdata['hd_logo']);

    if($hd_id) {

        delete_hdlogo($hd_logo, $oss_logo_path);

        $db->update('huodong', array_slice($pdata, 0, -3), "hd_id=$hd_id limit 1");

        //p(array_slice($pdata, 0, -3));

        $json['msg'] = '更新成功';
        logger("编辑活动, id:{$hd_id}");

    } else {

        $hd_id = $db->insert('huodong', $pdata);

        $table_name = "hd_$hd_id";
        create_hd_table($table_name);
        zd_add($table_name, $hd_zd_types, $hd_zd_pys);

        $json['msg'] = '添加成功';
        logger("添加活动, id:{$hd_id}");

    }

    $json['error'] = 0;
    echo json_encode($json);

}

function delete_hdlogo($hd_logo, $oss_logo_path) {
    if(!$hd_logo || !$oss_logo_path) {
        return;
    }
    if($oss_logo_path == $hd_logo) {
        return;
    }

    $imgname = basename($hd_logo);
    deleteObject("hdlogo/{$imgname}");
}


?>
