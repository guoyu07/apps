<?php

namespace APPS\test;

use APPS\user\oauth2\MApi;

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
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width,minimum-scale=0.1,maximum-scale=10">
	<link href="http://img.zhangchu.cc/css/reset.css" rel="stylesheet">
</head>
<body>
<?php

$ret = \Ko_Apps_Rest::VInvoke('user\\oauth2', 'GET', 'login/weibo');
echo \Ko_Html_Utils::SArr2html($ret);

?>
</body>
</html>