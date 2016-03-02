<?php

namespace APPS\test;

class MHtdocs_index
{
	public static function test()
	{
		echo 'index';
	}
}

?>
<!doctype html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width,minimum-scale=0.1,maximum-scale=10">
	<link href="http://img.yuxiaoyan.cn/css/reset.css" rel="stylesheet">
	<script src="http://img.yuxiaoyan.cn/js/jquery-1.11.2.min.js"></script>
	<script src="http://img.yuxiaoyan.cn/js/comet.js"></script>
</head>
<body>
welcome! it's a test!
<div id="result"></div>
<script>
	var comet = new ZLOG_Comet('polling.php', function(data){
		$('#result').append(data + "<br />\n\n");
	}, {});
	comet.polling();
</script>
</body>
</html>
