<?php

function get_pys($hd_zd_names) {
	if(!$hd_zd_names) return;
	$pys = array();
	foreach($hd_zd_names as $k => $v) {
		$py = zh2py::all2py($v, 'head', '').'_'.substr(md5($v), 0, 4);
		$pys[] = preg_replace("/[^\w\-]/", '', $py);
	}

	return $pys;
}

function create_hd_table($table_name) {
	$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
	  `u_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
	  `u_name` char(30) NOT NULL,
	  `post_time` int(10) unsigned NOT NULL,
	  `check_time` int(10) unsigned NOT NULL,
	  `is_check` tinyint(1) NOT NULL DEFAULT '0',
	  `msg` text CHARACTER SET utf8 NOT NULL,
	  PRIMARY KEY (`u_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";

	global $db;

	$db->query("$sql");

}

function zd_add($table_name, $hd_zd_types, $zds) {
	if(!$zds || !$hd_zd_types || !$zds) return;

	$arr = array();

	foreach($zds as $k => $v) {
		if($hd_zd_types[$k] == 'textarea' ) {
			$arr[] = "ADD  `$v` TEXT NOT NULL";
		} else {
			$arr[] = "ADD  `$v` CHAR( 30 ) NOT NULL";
		}
	}

	$zd_sql = implode(",", $arr);

	$sql = "ALTER TABLE  `$table_name` $zd_sql";

	//echo $sql;

	global $db;
	$db->query("$sql");

}


function zd_drop($table_name, $zds) {
	if(!$zds) return;

	foreach($zds as $k => $v) {
		$arr[] = "DROP  `$v`";
	}
	$zd_sql = implode(",", $arr);

	$sql = "ALTER TABLE  `$table_name` $zd_sql;";
//	echo $sql,'<br />';
//	die();

	global $db;
	$db->query("$sql");
}

function zd_edit($table_name, $hd_zd_types, $zds, $zds_edit) {
	if(!$zds || !$hd_zd_types) 	return;

	$zd_del = array_diff($zds, $zds_edit);
	$zd_add = array_diff($zds_edit, $zds);
	zd_add($table_name, $hd_zd_types, $zd_add);
	zd_drop($table_name, $zd_del);

}

function formater($str) {
	if(!$str) return;
	$arr = json_decode($str);
//	echo json_last_error();
//	p($arr);
	$re_arr = array();
	foreach($arr as $k => $v) {
		$re_arr['zd_name'][] = $v->name;
		$re_arr['zd_type'][] = $v->type;
		$re_arr['zd_val'][] = $v->val;
	}
	return $re_arr;
}

function str64_to_img($str64, $savepath='.') {
	$sarr = explode(",", $str64, 2);
	$exta = preg_split("#[/;]+#", $sarr[0]);
	$ext = strtolower($exta[1]);
	$allow_ext = array('jpg', 'png', 'gif', 'jpeg');

	$json['error'] = 1;

	if(!in_array($ext, $allow_ext)) {
		$json['msg'] = '图片类型错误';
		die(json_encode($json));
	}

	$img = base64_decode($sarr[1]);
	$newname = rand1() . '.' . $ext;

	/*加入图片路径解决写入失败问题*/
	$ret = file_put_contents("../img/{$newname}", $img);		//返回的是字节数
	//var_dump($newname);exit;

	if (!$ret)
	{
		$json['msg'] = '图片写入失败';
		die(json_encode($json));
	}
	return $newname;

}

function img2oss($filePath, $object) {
	
	$bucket = Config::OSS_TEST_BUCKET;
	$endpoint = Config::OSS_ENDPOINT;
	$ossClient = Common::getOssClient();
	//var_dump($endpoint);
	
//var_dump($ossClient->uploadFile($bucket, $object, $filePath, []));
//exit($bucket.'<br>'.$endpoint.'<br>'.$object.'<br>'.$filePath);
    $options = array();
    try {
        $ossClient->uploadFile($bucket, $object, $filePath, $options);
    } catch (OssException $e) {
        printf($e->getMessage() . "\n");
    }

	unlink($filePath);
	return "https://{$bucket}.{$endpoint}/{$object}";

}

function deleteObject($object)
{
	$bucket = Config::OSS_TEST_BUCKET;
	$ossClient = Common::getOssClient();

    try {
        $ossClient->deleteObject($bucket, $object);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
}


?>
