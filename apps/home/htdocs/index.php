<?php

namespace APPS\home;

$num = 10;
$msglist = \Ko_App_Rest::VInvoke('sysmsg', 'GET', 'item/', array(
	'page' => array(
		'num' => $num,
		'boundary' => '0_0',
	),
));
$page = $msglist['page'];
$msglist = $msglist['list'];

$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
$render = $render['key'];
$render->oSetTemplate('index.html')
	->oSetData('msglist', $msglist)
	->oSetData('page', $page)
	->oSend();
