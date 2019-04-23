<?php

function printr($str)
{
	if(is_array($str)) {
		echo '<pre>';
		print_r($str);
		echo '</pre>';
		return;
	}
		echo '<pre>';
		echo $str;
		echo '</pre>';
}

function p($str)
{
	if (is_array($str))
	{
		echo '<pre>';
		print_r($str);
		echo '</pre>';
		return;
	}
	echo '<pre>';
	echo $str;
	echo '</pre>';
}


function inistr($str, $default = '')
{
	return $str = isset($str) ? $str : $default;
}

function pretty_arr($arr)
{
	echo "<pre>";
	var_export($arr);
	echo "</pre>";
}

function removeBacktraceArg($a)
{
	foreach($a as $k => $v)
	{
		if ($k == 'args' || $k == 'object') unset($a[$k]);
	}
	return $a;
}


function get_current_url()
{
	return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':' . $_SERVER['SERVER_PORT']) . $_SERVER['REQUEST_URI'];
}


function redirect($url) {
	if($url== -1 ) $url = $_SERVER['HTTP_REFERER'];
	header("Location:$url");
	exit;
}


function is_email($s)
{
	return preg_match('/^[\w\.\-]+@[\w\-]+\.[a-zA-Z\.]{2,10}$/', $s);
}

function is_mobile($phone)
{
	return preg_match("/^1[34578]{1}\d{9}$/", $phone);
}

function is_ip($ip, $opt=0) {
	if($opt == 'ipv4') return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	if($opt == 'ipv6') return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	if($opt == 'ipv46') return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
	if($opt == 'remote') return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	return filter_var($ip, FILTER_VALIDATE_IP);
}

function uploader($conf)
{
	if (!$_FILES) return;
	$upfile = $conf['field'] ? $_FILES[$conf['field']] : current($_FILES);

	extract($upfile);

	if (!$tmp_name)
	{
		return;
	}

	if ($error)
	{
		msg(upload_error($error));
	}

	if ($error > 0 || !is_uploaded_file($tmp_name))
	{
		msg('上传失败，请重试');
	}

	$default = array(
		'maxSize' => 3000 * 1024 ,
		'exts' => array('jpg', 'gif', 'png', 'jpeg') ,
		'rootPath' => './img/' ,
		'saveName' => rand1(),
		'maxWH' => 0 ,
		'water' => 0 ,
		'replace' => true ,
		);

	$conf = array_merge($default, $conf);
	extract($conf);

	$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

	if (!in_array($ext, $exts))
	{
		msg('上传失败，文件类型错误');
	}

	if (in_array($ext, array('jpg', 'gif', 'png', 'jpeg', 'bmp')))
	{
		$info = getimagesize($tmp_name);
		if ($info[2] < 1)
		{
			msg('文件类型错误，上传失败');
		}
	}

	if ($maxSize && $size > $maxSize)
	{
		msg('上传失败，文件大小不要超过' . bsize($maxSize));
	}

	$save_dir = $rootPath . '/';
	$save_dir = str_replace('//', '/', $save_dir);
	if (!is_dir($save_dir)) mkdir($save_dir, 0755, true);

	if ($saveName)
	{
		$savePath = $save_dir . $saveName . '.' . $ext;
	}
	else
	{
		$savePath = $save_dir . $name; //原名保存
	}

	if (!$replace && is_file($savePath))
	{
		msg("存在同名文件： " . basename($savePath));
	}

	if (!move_uploaded_file($tmp_name, $savePath))
	{
		msg("移动文件失败");
	}

	list($maxW, $maxH) = $maxWH;
	if (($maxW && $info[0] > $maxW) || ($maxH && $info[1] > $maxH)) resetImg($savePath, $maxW, $maxH);
	return $savePath;
}


function resizeImg($iName, $toW, $toH, $toName = 0)
{
	if ($toW < 0 && $toH < 0) return;
	list($iw, $ih) = getimagesize($iName);
	if (!$toH)
	{
		$toH = round($ih * $toW / $iw);
	}
	else if (!$toW)
	{
		$toW = round($iw * $toH / $ih);
	}
	// echo $toW,'<br />';
	// echo $toH,'<br />';
	$save_name = $toName ? $toName : $iName;
	$image = new \Think\Image();
	$image->open($iName);
	$image->thumb($toW, $toH, 6)->save($save_name);
}

function upload_error($error)
{
	if (empty($error)) return;
	switch ($error)
	{
		case '1':
			$error = '超过php.ini允许的大小。';
			break;
		case '2':
			$error = '超过表单允许的大小。';
			break;
		case '3':
			$error = '图片只有部分被上传。';
			break;
		case '4':
			$error = '请选择图片。';
			break;
		case '6':
			$error = '找不到临时目录。';
			break;
		case '7':
			$error = '写文件到硬盘出错。';
			break;
		case '8':
			$error = 'File upload stopped by extension。';
			break;
		case '999':
		default:
			$error = '未知错误。';
	}
	return $error;
}

function getOs()
{
	if (!empty($_SERVER['HTTP_USER_AGENT']))
	{
		$OS = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/win/i', $OS))
		{
			$OS = 'Windows';
		}elseif (preg_match('/mac/i', $OS))
		{
			$OS = 'MAC';
		}elseif (preg_match('/linux/i', $OS))
		{
			$OS = 'Linux';
		}elseif (preg_match('/unix/i', $OS))
		{
			$OS = 'Unix';
		}elseif (preg_match('/bsd/i', $OS))
		{
			$OS = 'BSD';
		}
		else
		{
			$OS = 'Other';
		}
		return $OS;
	}
	else
	{
		return "获取访客操作系统信息失败！";
	}
}

function where($cond) {
	$cond = trim($cond);
	$cond = trim($cond, "where,and");
	return $cond;
}


function getHttpInfo($url, $timeout = 3)
{
	$ch = curl_init();
	$opts = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $url,
		CURLOPT_HEADER => 0,			// 不要header
		CURLOPT_NOBODY => 1,
		CURLOPT_TIMEOUT => $timeout
		);
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$status = curl_getinfo($ch);		// find HTTP status
	curl_close($ch);					// close handle
	return $status;
}

?>