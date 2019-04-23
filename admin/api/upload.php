<?php

require '../conn.php';

if ($_FILES["file"]["error"]) {
    echo $_FILES["file"]["error"];
} else {
    //没有出错
    //加限制条件
    //判断上传文件类型为png或jpg且大小不超过1024000B
    if (($_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/jpeg") && $_FILES["file"]["size"] < 1024000) {
        //防止文件名重复
        $filename = "../images/" . time() . $_FILES["file"]["name"];
        //转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
        $filename = iconv("UTF-8", "gb2312", $filename);
        //检查文件或目录是否存在
        if (file_exists($filename)) {
            echo "该文件已存在";
        } else {
            //保存文件,   move_uploaded_file 将上传的文件移动到新位置
            move_uploaded_file($_FILES["file"]["tmp_name"], $filename); //将临时地址移动到指定地址
            $hd_id = intval($_POST['hd_id']);
            $filename = substr($filename,3);
	    $pdata = array(
                'wx_num'  => $_POST['wx_num'],
                'img_url' => $filename,
            );
            if ($hd_id) {

                delete_hdlogo($hd_logo, $oss_logo_path);

                $db->update('info', array_slice($pdata, 0, -3), "hd_id=$hd_id limit 1");

                //p(array_slice($pdata, 0, -3));

                $json['msg'] = '更新成功';

            } else {

                $hd_id = $db->insert('info', $pdata);

                header('content-type:text/html;charset=utf-8');
                $url='https://zzlw1688.com/admin/index.html';

                echo "<script>window.location.href='$url';</script>";
            }
        }
    } else {
        echo "文件类型不对";
    }
}
