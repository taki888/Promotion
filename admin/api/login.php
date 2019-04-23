<?php

require('../conn.php');
if(!IS_AJAX) exit('false');
if ($_POST['u_name'] && $_POST['u_pwd'])
{
	extract($_POST);
	$pwd = pw($u_pwd);
	$ip = getip();
	$token = md5(sha1($ip . $_SERVER['HTTP_USER_AGENT']));

	//$u_name = $db->clean($u_name);
//var_dump($u_name);
	$row = $db->one("select * from user where u_name='$u_name' limit 1");
//print_r($row);die;
//$r_u_id=$row['u_id'];
//print_r($row);
//var_dump($pwd);
//var_dump($row['password']);
//var_dump($row['u_pwd'] === $pwd);
	if ($row['password'] === $pwd)
	{

		extract($row, EXTR_PREFIX_ALL, 'r');
		$db->query("update user set u_lastlogin=$date, u_ip='$ip' where u_id = $r_u_id limit 1");

		$_SESSION['u_id'] = $r_u_id;
		$_SESSION['u_name'] = $r_u_name;
		$_SESSION['u_true_name'] = $r_u_true_name;
		$_SESSION['u_type'] = $r_u_type;
		$_SESSION['u_ip'] = $r_u_ip;
		$_SESSION['u_lastlogin'] = $r_u_lastlogin;
		$_SESSION['isTrueLogin'] = $token;
		$json['error'] = 0;
		$json['msg'] = '登录成功';
		logger("登录了系统");
	}
	else
	{
		$json['error'] = 1;
		$json['msg'] = '用户名与密码不匹配';
	}
}

//session_write_close();
echo json_encode($json);
session_write_close();

?>
