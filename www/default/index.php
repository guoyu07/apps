<?php

$num = 10;
$msglist = Ko_Apps_Rest::VInvoke('sysmsg', 'GET', 'item/', array(
	'page' => array(
		'num' => $num,
		'boundary' => '0_0',
	),
));
$page = $msglist['page'];
$msglist = $msglist['list'];

$render = new KRender_default;
$render->oSetTemplate('default/index.html')
	->oSetData('msglist', $msglist)
	->oSetData('page', $page)
	->oSend();
