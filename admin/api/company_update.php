<?php

require '../conn.php';

if (!IS_AJAX) {
    exit('false');
}

isUserLogin(array(1));
session_write_close();

if ($_GET['id']) {
    $id            = intval($_GET['id']);
    $row           = $db->one("select * from info where id=$id limit 1");
    $json['data']  = $row;
    $json['error'] = 0;
    echo json_encode($json);
    die();
}

if ($_POST['wx_num']) {

    extract($db->clean2($_POST));

    $pdata = array(
        'wx_num'  => $_POST['wx_num'],
        'img_url' => $_POST['img_url'],
    );

    if ($id) {
        $id = intval($id);
        $db->update('info', $pdata, "id=$id limit 1");
        $json['msg'] = '更新成功';
        logger("更新用户信息, id:{$id}");

    }

    $json['error'] = 0;
    echo json_encode($json);

}
