<?php

namespace APPS\home;

$num = 10;
$msglist = \APPS\sysmsg\MFacade_Api::getIndexList('0_0', $num, $next, $next_boundary);
$page = compact('num', 'next', 'next_boundary');

$render = new \APPS\render\MFacade_default();
$render->oSetTemplate('index.html')
	->oSetData('msglist', $msglist)
	->oSetData('page', $page)
	->oSend();
