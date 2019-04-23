<?php

header('Content-Type:text/html;charset=UTF-8');
error_reporting(1);
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);                                       //调试
session_save_path(__DIR__ . '/session');

define('ROOT', '/');
define('WEBROOT', 'http://' . $_SERVER["HTTP_HOST"] . ROOT);
define('WEBROOT1', substr(WEBROOT, 0, -1));
define('DOCROOT', dirname(__FILE__) . '/');
define('DOCROOT1', dirname(__FILE__));
define('TEMPDIR', DOCROOT . 'temp/');
define('CACHEDIR', DOCROOT . 'cache/');

define('SALT', '2HpuNVrkwI1GYTOMiJqrBLlijtlpGB7gnTt89K8d');
define('UC_KEY', 'eB9H2wPEYoU6VeYJT3SnnaPNpENFFBpPSQvMqRB7');

define('IS_AJAX1', strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' );

require_once(DOCROOT . 'class/inc/var.php');
require_once(DOCROOT . 'class/inc/funtion.php');

define('R', is_file(DOCROOT . '.htaccess'));
define('IS_POST', $_SERVER['REQUEST_METHOD'] === 'POST');
define('IN_POST', ($_SERVER['REQUEST_METHOD'] === 'POST') && (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false));

spl_autoload_register( '__autoload' );

function __autoload($name)
{
    require(DOCROOT . 'class/' . $name . '.php');
}

$dbc = array(
    'host' => 'localhost',
        'port' => 3306,
        'dbname' => 'flyer',
        'user' => 'root',
        'password' => '123456',
        'charset' => 'utf8',
    );

session_set_cookie_params(0, ROOT);
if (!isset($_SESSION)) session_start();

if (PHP_VERSION > '5.2')
{
    $db = new Mysqli2($dbc);
}
else
{
    $db = new Mysql($dbc);
}

//echo $db->clean("a'a") ,'<br />';
//echo $db->clean("a'\"a") ,'<br />';
//echo $db->clean('a"\a') ,'<br />';

//printr($db->repair("admin"));
//printr($db->optimize("admin"));

//echo '<pre>';
//print_r ($db);
//echo '</pre>';

//echo mysqli_character_set_name($db->link);
//echo $db->version(),'<br />';
// printr($db->tables(''));
// printr($db->get_fields("admin"));
// $tables = $db->tables();
// $backer = new dbBack($dbc['dbname'], 'aaa.sql', 1);
// $backer-> backtable('admin');
// $backer-> backtable('ad');
// $backer-> backtable('news');
// $backer -> backdb();

if(class_exists('redis', false)) {
    $redis = new redis();
    $redis->connect('127.0.0.1', 6379);
    //$redis->auth('RnOcefvsxituh7eBoG9Hocuz1VdeJS7MfkFf9qlgbgKG9R2wiLdoFczMrSW');
}

// $ret = $db->query("desc admin");
// print_r($db->fetch_assoc($ret));

// var_dump($redis);



function rand1()
{
    list($u, $s) = explode(' ', microtime());
    return $s . substr($u, 2, 6) . mt_rand(10000, 99999);
}

function rand2()
{
    list($u, $s) = explode(' ', microtime());
    return date("YmdHis") . substr($u, 2, 6) . mt_rand(10000, 99999);
}

function mysqlClean($s)
{
    return (is_array($s)) ? array_map(__FUNCTION__, $s):mysql_real_escape_string($s);
}

function mysqlClean1($s)
{
    return (is_array($s)) ? array_map(__FUNCTION__, $s) : mysql_real_escape_string(trim($s));
}

function mysqlClean2($s)
{
    return (is_array($s)) ? array_map(__FUNCTION__, $s) : mysql_real_escape_string(strip_tags(trim($s)));
}

function pageList($num, $pagesize, $curr_page, $query = 1)
{
    if ($num <= $pagesize) return ;
    $multipage = '';
    $page = 10 ;
    $offset = 4;
    $pages = ceil ($num / $pagesize);
    $from = $curr_page - $offset ;
    $to = $curr_page + $page - $offset - 1 ;

    $qustr = '?';
    if($query && $_SERVER['QUERY_STRING']) {
        $q = $_SERVER['QUERY_STRING'];
        $q = preg_replace('/&?page=\d*/', '', $q);
        if($q) $qustr .= $q.'&';
    }

    if ($page > $pages)
    {
        $from = 1 ;
        $to = $pages ;
    }
    else
    {
        if ($from < 1)
        {
            $from = 1;
            $to = $curr_page + 1 - $from ;
            if (($to - $from) < $page && ($to - $from) < $pages)
            {
                $to = $page ;
            }
        }elseif ($to > $pages)
        {
            $from = $curr_page - $pages + $to ;
            $to = $pages ;
            if (($to - $from) < $page && ($to - $from) < $pages) $from = $pages - $page + 1 ;
        }
    }

    if (!$curr_page) $curr_page = 1;
    $pre = $curr_page-1;

    if($pre) {
        if($pre>5) $multipage .= '<a class="prepage" href="' . $qustr .  "page=1\">首页 </a>";
        $multipage .= '<a class="prepage" href="' . $qustr . 'page=' . $pre . "\"> 上一页 </a>";
    }

    for ($i = $from ; $i <= $to ; $i ++)
    {
        if ($i != $curr_page) $multipage .= '<a href ="' . $qustr . 'page=' . $i . "\">$i</a>";
        else $multipage .= '<a class="currentPage" id="currentPage">' . $i . '</a>';
    }
    if ($curr_page < $pages)
    {
        $next = $curr_page + 1;
        $multipage .= '<a class="nextpage" href="' . $qustr . 'page=' . $next . '"> 下一页 </a> ';
    }

    if($pages > 10 && $curr_page != $pages  ) {
        $multipage .= '<a href="' . $qustr . 'page=' . $pages . '">尾页</a> ';
    }

    return $multipage ;
}

function pageList1($num, $pagesize, $curr_page, $ru)
{
    $multipage = '';
    if ($num <= $pagesize) return ;
    $page = 10 ;
    $offset = 5;
    $pages = ceil ($num / $pagesize);
    $from = $curr_page - $offset ;
    $to = $curr_page + $page - $offset - 1 ;
    if ($page > $pages)
    {
        $from = 1 ;
        $to = $pages ;
    }
    else
    {
        if ($from < 1)
        {
            $from = 1;
            $to = $curr_page + 1 - $from ;
            if (($to - $from) < $page && ($to - $from) < $pages)
            {
                $to = $page ;
            }
        }elseif ($to > $pages)
        {
            $from = $curr_page - $pages + $to ;
            $to = $pages ;
            if (($to - $from) < $page && ($to - $from) < $pages) $from = $pages - $page + 1 ;
        }
    }

    if (!$curr_page) $curr_page = 1;
    $pre = $curr_page-1;

    if($pre) {
        if($pre>5) $multipage .= '<a class="prepage" href="' . $ru .  ".html\">首页 </a>";
        $multipage .= '<a class="prepage" href="' . $ru . '-' . $pre . "\">上一页</a>";
    }


    for ($i = $from ; $i <= $to ; $i ++)
    {
        if ($i != $curr_page) $multipage .= '<a href ="' . $ru . '-' . $i . ".html\">$i</a>";
        else $multipage .= '<a class="currentPage" id="currentPage">' . $i . '</a>';
    }

    if ($curr_page < $pages)
    {
        $next = $curr_page + 1;
        $multipage .= '<a class="nextpage" href="' . $ru . '-' . $next . '.html">下一页</a> ';
    }
    else
    {
        $multipage .= '<a class="nextpage" href="' . $ru . '.html">下一页</a> ';
    }

    if($pages > 10 && $curr_page != $pages  ) {
        $multipage .= '<a href="' . $ru . '-' . $pages . '.html">尾页</a> ';
    }


    return $multipage ;
}

/**
 * $iName原图片路径, $toW, $toH处理后的宽高
 * $toName要转换成图片名，不填则不转换
 * toW为0时，按高等比例裁剪
toH为0时，按宽等比例裁剪
 */
function resetImg($iName, $toW, $toH, $toName = 0)
{
    if(!$iName) return;
    if ($toW < 0 && $toH < 0) return;
    list($iw, $ih, $it) = getimagesize($iName);
    if ($toH == 0)
    {
        $toH = round($ih * $toW / $iw);
    }
    else if ($toW == 0)
    {
        $toW = round($iw * $toH / $ih);
    }
    $canvas = imagecreatetruecolor($toW, $toH);
    ini_set('gd.jpeg_ignore_warning', 1);
    switch ($it)
    {
        case 1: $source = imagecreatefromgif($iName);
            break;
        case 2: $source = imagecreatefromjpeg($iName);
            break;
        case 3: $source = imagecreatefrompng($iName);
            break;
        default: return;
    }
    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $toW, $toH, $iw, $ih); //用于尺寸转换
    if ($toName) rename($iName, $toName); //用于格式转换
    $name = $toName ? $toName : $iName;
    $exts = explode('.', $name);
    $ext = strtolower(end($exts));
    if ($ext == 'gif') imagegif($canvas, $name);
    else if ($ext == 'jpg' || $ext == 'jpeg') imagejpeg($canvas, $name, 80);
    else if ($ext == 'png') imagepng($canvas, $name);
    imagedestroy($canvas);
    imagedestroy($source);
}

function getip()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) $ip = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) $ip = getenv('HTTP_X_FORWARDED_FOR');
    else if ($_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = 'unknown';
    return $ip;
}

function picExt($name)
{
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    return $ext === 'jpeg' ? 'jpg' : $ext;
}

function extractImgSrc($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $tags = $dom->getElementsByTagName('img');
    $a = array();
    foreach ($tags as $tag)
    {
        $a[] = $tag->getAttribute('src');
    }
    return $a;
}

/**
 * 删除kinkeditor的图片路径
 */
function delImg($html)
{
    $srcs = extractImgSrc($html);
    for($i = 0; $i < count($srcs); $i++)
    {
        $src = str_replace(WEBROOT, '', $srcs[$i]);
        $src = DOCROOT . substr_replace($src, '', 0, strlen(ROOT));
        if (is_file($src)) unlink($src);
    }
}

function enquote($str)
{
    return str_replace('"', '&quot;', $str);
}

function delDir($dir, $del = 0)
{
    if (!file_exists($dir))
    {
        return ;
    }
    $dh = opendir($dir);
    while (($file = readdir($dh)) !== false)
    {
        if ($file != "." && $file != "..")
        {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath))
            {
                unlink($fullpath);
            }
            else
            {
                delDir($fullpath , 1);
            }
        }
    }
    closedir($dh);
    return $del ? rmdir($dir) : 1;
}

function limiter($ps = 100)
{
    global $page, $pagesize;
    $pagesize = $ps;
    $page = intval($_GET['page']) ? intval($_GET['page']) : 1;
    $start_row = ($page-1) * $pagesize;
    $pager = "limit $start_row, $pagesize";
    return $pager;
}

function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;

    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), - $ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++)
    {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++)
    {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++)
    {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE')
    {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
        {
            return substr($result, 26);
        }
        else
        {
            return '';
        }
    }
    else
    {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
// $ucstr = uc_authcode('6\t1434504189',1, 0,60);
// echo $ucstr,'<br />';
// echo uc_authcode($ucstr,'DECODE', 0),'<br />';

function sorter($zd)
{
    global $db;
    $sort = $db->clean($_GET['sort']);
    $order = $db->clean($_GET['order']);
    $order1 = $order == 'asc' ? 'asc' : 'desc';
    if ($sort && $order)
    {
        $sortby = $sort . ' ' . $order1;
    }
    else
    {
        $sortby = "$zd desc";
    }
    return $sortby;
}

function alister($arr, $key = "")
{
    if ($key)
    {
        foreach($arr as $k => $v)
        {
            echo '<option value="', $v, '">', $v, '</option>';
        }
        return;
    }

    foreach($arr as $k => $v)
    {
        echo '<option value="', $k, '">', $v, '</option>';
    }
}

function isAdminLogin($allow_type = null, $outpage = 'logout.php')
{
    if (!isset($_SESSION)) session_start();
    $admin_type = intval($_SESSION['admin_type']);
    $ua = md5(sha1(getip() . $_SERVER['HTTP_USER_AGENT']));

    if (!$admin_type || $_SESSION['isTrueLogin'] !== $ua)
    {
        header("Location:$outpage");
        exit();
    }

    if (is_array($allow_type) && $allow_type)
    {
        $qx = in_array($admin_type, $allow_type);
        if (!$qx)
        {
            msg('没有权限', -1);
        }
    }

    lazy_logout();
}

function msg($msg = '', $url = -1, $type = 0)
{
    if (!$url || $url == -1)
    {
        $url = "javascript:history.back(-1);";
    }

    $style = 'background:#F2DEDF;border:1px solid #e1bcc2;color:#a40';
    if($type == 1) {
        $style = 'background:#DEF0D8;border:1px solid #bcd1aa;color:#3C763C';
    }

print <<<EOT
<!DOCTYPE html>
<html>
<head>
<title>跳转提示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
*{margin:0;padding:0;border:none;}
body{background:#fff;font-family:"Microsoft YaHei",Tahoma,Helvetica;font-size:14px;color:#333;}
body a{text-decoration:none;color:#000;}
body a:hover{text-decoration:underline;}
#msgbox{width:50%;max-width:800px;margin:7% auto;$style;border-radius:3px;padding:20px 30px;overflow-x:hidden;}
#msgcenter{display:block;margin-top:10px;font-size:14px;}
.msg{line-height:1.7;padding:5px 0;}
</style>
</head>
<body>
<div id="msgbox">
    <div class="msg">$msg</div>
    <center id="msgcenter"><a href="$url">返回上一页</a></center>
</div>

</body></html>
EOT;
die();
}

/**
 *
 * @lazytime ： 多久不活动就退出
 */
function lazy_logout($lazytime = 7200)
{echo '22';sleep(5);
    $ctime = time();
    $logout_time = $_SESSION['logout_time'];
    if ($logout_time && $ctime > $logout_time)
    {
        header('Location:logout.php');
        exit();
    }
    $_SESSION['logout_time'] = $ctime + $lazytime;
}

function download($file)
{
    // var_dump($file);
    if (!file_exists(win2gbk($file))) return ;
    header('Cache-Control: max-age=0');
    header("Content-Type: application/force-download");
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename=' . basename(win2gbk($file)));
    ob_clean();
    flush();
    readfile(win2gbk($file));
    exit;
}

function dbbacker($bakname)
{
    global $dbc;
    $dbname = $dbc['dbname'];
    $dbuser = $dbc['user'];
    $dbpw = $dbc['password'];
    $shell = function_exists('shell_exec');
    if ($shell && PHP_OS === 'Linux')
    {
        $sql = "mysqldump -u$dbuser -p$dbpw $dbname --skip-lock-tables | gzip > ${bakname}.gz";
        shell_exec($sql);
    }
    else
    {
        $backer = new dbback($dbname, $bakname);
        $backer->backdb();
    }
}

function isshow($str)
{
    echo $str == "1" ? '<span>是</span>' : '<span class="red">否</span>';
}

function date_sql($zd = 'date', $date1 = '', $date2 = '')
{
    $date1 = strtotime($date1 ? $date1 : $_GET['date1']);
    $date2 = strtotime($date2 ? $date2 : $_GET['date2']);
    if ($date1 && $date2)
    {
        $time2 = $date2 + 24 * 3600;
        $cond = "$zd>=$date1 and $zd<$time2";
    }
    else if ($date1 || $date2)
    {
        $time1 = $date1 ? $date1 : $date2;
        $time2 = $time1 + 24 * 3600;
        $cond = "$zd>=$time1 and $zd<$time2";
    }
    $cond and $cond = " and $cond ";
    return $cond;
}

function win2gbk($str)
{
    if (PHP_OS == 'WINNT') return iconv('UTF-8', 'GBK', $str);
    return $str;
}

function win2utf8($str)
{
    if (PHP_OS == 'WINNT') return iconv('GBK', 'UTF-8', $str);
    return $str;
}

function readDir1($dir, $type = null)
{
    $dir = str_replace(array('\\', '//'), array('/', '/'), $dir);
    if (PHP_OS == 'WINNT') $dir = iconv('UTF-8', 'GBK', $dir);
    if (!is_dir($dir)) return;
    $noxr = PHP_OS == 'Linux' ? !is_readable($dir) || !is_executable($dir) : !is_readable($dir);
    if ($noxr)
    {
        return;
    }

    if (substr($dir, -1) != '/') $dir .= '/';
    $da = array();

    $dh = opendir($dir);
    while (($f = readdir($dh)) !== false)
    {
        if ($f == '.' || $f == '..') continue;
        $fileType = count(explode('.', $f)) == 1 ? '' : strtolower(end(explode('.', $f)));
        $isType = empty($type) ? 1 : in_array($fileType, $type, true);
        if (is_dir($dir . $f))
        {
            $da[strtolower($f)] = $f;
        }
    }
    closedir($dh);

    ksort($da);
    return $da;
}

function utf8str($str)
{
    if (mb_check_encoding($str, 'UTF-8')) return $str; //UTF-8
    else if (mb_check_encoding($str, 'GBK')) $utf8str = iconv('GBK', 'UTF-8//TRANSLIT', $str); //ansi
    else if (mb_check_encoding($str, 'UTF-16LE')) $utf8str = iconv('UTF-16LE', 'UTF-8//TRANSLIT', $str); // unicode
    return $utf8str;
}

function pw($pw)
{
    return md5(sha1($pw));
}
// echo pw("123456");
function db_size()
{
    global $db;
    $result = $db->query('show table status');
    $dbsize = 0;
    while ($row = $db->fetch_assoc($result))
    {
        $dbsize += $row['Data_length'] + $row['Index_length'];
    }
    return bsize($dbsize);
}

function bsize($str)
{
    foreach (array('', 'K', 'M', 'G') as $i => $k)
    {
        if ($str < 1024) break;
        $str /= 1024;
    }
    return sprintf("%5.2f %sB", $str, $k);
}

function ifile($filename, $ext = 0)
{
    $arr = pathinfo($filename);
    return $ext ? $arr['extension'] : $arr['filename'];
}

function curl_download($url, $savePath, $ch1 = null)
{
    $ch = $ch1 ? $ch1 : curl_init();
    $fp = fopen($savePath, 'wb');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(25, 40) . '.0.' . rand(125, 2240) . ' Safari/537.36');
    $output = curl_exec($ch);
    fclose($fp);
    if (!$ch1) curl_close($ch);
    return $output;
}

function curl_get_contents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/' . rand(25, 40) . '.0.' . rand(125, 2240) . ' Safari/537.36');
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function wget($url, $savePath = "")
{
    if (PHP_OS != 'Linux' || !function_exists('exec')) return ;
    exec("wget '$url'", $output, $ret);
    if (isset($savePath))
    {
        $file = basename($url);
        $rr = rename($file, $savePath);
    }
    return !$ret;
}

function check_file_code($file)
{
    $str = file_get_contents($file);
    $codes = array('ASCII', 'GBK', 'UTF-8', 'UTF-16', 'UTF-32', 'UTF-16LE', 'UCS-2');
    foreach($codes as $code)
    {
        if (mb_check_encoding($str, $code)) return $code;
    }
}



function url2($type, $id)
{
    return "$type-$id";
}

if (defined('R'))
{
    function url($type, $id, $path = '')
    {
        return ROOT . "$type-{$id}.html";
    }
}
else
{
    function url($type, $id, $path = '')
    {
        // if(!$path) $path = ROOT;
        if ($type == 'news')
        {
            return ROOT . 'news.php?id=' . $id;
        }

        if ($type == 'news1')
        {
            return ROOT . 'news1.php?id=' . $id;
        }

        if ($type == 'newsc')
        {
            return ROOT . 'newslist.php?id=' . $id;
        }

        if ($type == 'newsp')
        {
            return ROOT . 'newsp.php?id=' . $id;
        }

        if ($type == 'newspc')
        {
            return ROOT . 'newslistp.php?id=' . $id;
        }

        if ($type == 'list')
        {
            return ROOT . 'list.php?id=' . $id;
        }
    }
}

function check_vcode()
{
    if (!$_POST['vcode'] || strtolower($_SESSION['vcode']) != strtolower($_POST['vcode']))
    {
        msg("验证码错误");
    }
    unset($_SESSION['vcode']);
}


function cachefile($file, $key, $ttl, $engine = 'file')
{
    $cp = new Cache($key, $ttl, $engine);
    $cache = $cp->start(1);
    if ($cache)
    {
        echo $cache;
    }
    else
    {
        require($file);
    }
    $cp->end();
}

function cachefile1($file, $cfile = '')
{
    $cfile = CACHEDIR . ($cfile ? $cfile : $file);
    $cached = is_file($cfile);
    if ($cached && !$_GET['upcache'])
    {
        echo '<!---------------------- ttl ----------------------->';
        require($cfile);
    }
    else
    {
        ob_start();
        require(DOCROOT . $file);
        $content = ob_get_contents();
        ob_end_flush();
        file_put_contents($cfile, $content);
    }
}

function flock_write($file, $content) {
    $fp = fopen($file, "w+");
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $content);
        flock($fp, LOCK_UN); // 释放锁定
    } else {
        echo "Couldn't lock the file !";
    }
    fclose($fp);
}


function is_repeat_post($token = '__hash__') {

    if (!isset($_SESSION))
    {
        session_start();
    }

    if(!$_SESSION[$token]) {
        $_SESSION[$token] = md5(microtime());
    }

    if($_POST) {
        if($_SESSION[$token] == $_POST[$token]){
            $_SESSION[$token] = md5(microtime());
        }else{
            return 1;
        }
    }

    return '<input type="hidden" name="'.$token.'" value="'.$_SESSION[$token].'" />';
}

function isUserLogin($allow_type = null)
{
    //if (!isset($_SESSION)) session_start();
    $admin_type = intval($_SESSION['u_type']);

    $ua = md5(sha1(getip() . $_SERVER['HTTP_USER_AGENT']));

    if (!$admin_type || $_SESSION['isTrueLogin'] !== $ua)
    {
        json_error("未登录");
    }

    if (!$allow_type) {
        return;
    }

    if (!in_array($admin_type, $allow_type))
    {
        json_error("没有权限");
    }

    //lazy_logout();

}

function lazy_logout1($lazytime = 7200)
{
    $ctime = time();
    $logout_time = $_SESSION['logout_time'];
    if ($logout_time && $ctime > $logout_time)
    {
        json_error("登录过期");
    }
    $_SESSION['logout_time'] = $ctime + $lazytime;
}

function json_error($msg, $die=1) {
    $json['error'] = 1;
    $json['msg'] = $msg;
    echo json_encode($json);
    if($die) die();
}

function json_ok($msg, $die=1) {
    $json['error'] = 0;
    $json['msg'] = $msg;
    echo json_encode($json);
    if($die) die();
}


function logger($msg) {
    $date = time();
    $t1 = date('m-d H:i', $date);
    $user = $_SESSION['u_name'];
    $logfile = date('Ym',$date) . '.txt';
    $logdir = DOCROOT. "logga/";
    $file = $logdir . $logfile;
    if(!is_dir($logdir)) {
        mkdir($logdir, 0755, true);
    }

    $log = "{$t1} {$user}  $msg\r";
    file_put_contents($file, $log, FILE_APPEND);

}


?>
