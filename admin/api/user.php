<?php
require('../conn.php');

if(!IS_AJAX) exit('false');	

isUserLogin();

//var_dump($_SESSION);
$json = array();
$data = array();


$json['error'] = 0;
$json['msg'] = '';

$data['u_id'] = $_SESSION['u_id'];
$data['u_name'] = $_SESSION['u_name'];
$data['u_type'] = $_SESSION['u_type'];
$data['u_true_name'] = $_SESSION['u_true_name'];
$data['u_ip'] = $_SESSION['u_ip'];
$data['u_lastlogin'] = $_SESSION['u_lastlogin'];

$json['data'] = $data;

echo json_encode($json);
?>