<?php

require('../conn.php');

if(!IS_AJAX) exit('false');
//if(!IS_POST) exit('false');

isUserLogin();
session_write_close();

$apply_arr = $redis->hgetall("hd_apply");

//print_r($apply_arr);

echo json_encode($apply_arr);


?>