<?php
	$htmlData = '';
if (!empty($_POST['actIntro'])) {
    if (get_magic_quotes_gpc()) {
        $htmlData = stripslashes($_POST['actIntro']);
    } else {
        $htmlData = $_POST['actIntro'];
    }
}
$actRulesData = '';
if (!empty($_POST['actRules'])) {
    if (get_magic_quotes_gpc()) {
        $actRulesData = stripslashes($_POST['actRules']);
    } else {
        $actRulesData = $_POST['actRules'];
    }
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>KindEditor PHP</title>
	<link rel="stylesheet" href="/js/kindeditor/themes/default/default.css" />
	<link rel="stylesheet" href="/js/kindeditor/plugins/code/prettify.css" />
	<script charset="utf-8" src="/js/kindeditor/kindeditor-all-min.js"></script>
	<script charset="utf-8" src="/js/kindeditor/lang/zh-CN.js"></script>
	<script charset="utf-8" src="/js/kindeditor/plugins/code/prettify.js"></script>
	<script>
		KindEditor.ready(function(K) {
			var actIntro = K.create('textarea[name="actIntro"]', {
				cssPath : '/js/kindeditor/plugins/code/prettify.css',
				uploadJson : '/js/kindeditor/php/upload_json.php',
				fileManagerJson : '/js/kindeditor/php/file_manager_json.php',
				allowFileManager : true
			});
            var actRules = K.create('textarea[name="actRules"]', {
                cssPath : '/js/kindeditor/plugins/code/prettify.css',
                uploadJson : '/js/kindeditor/php/upload_json.php',
                fileManagerJson : '/js/kindeditor/php/file_manager_json.php',
                allowFileManager : true
            });
			prettyPrint();
		});
	</script>
</head>
<body>
	<?php echo $htmlData; ?>
	<form name="example" method="post" action="demo.php">
        <textarea name="actIntro" style="width:700px;height:200px;visibility:hidden;"><?php echo htmlspecialchars($htmlData); ?></textarea>
        <br />
        <?php echo $actRulesData; ?>
        <textarea name="actRules" style="width:700px;height:200px;visibility:hidden;"><?php echo htmlspecialchars($actRulesData); ?></textarea>
        <br />
        <input type="submit" name="button" value="提交内容" /> (提交快捷键: Ctrl + Enter)
	</form>
</body>
</html>

