<?php

namespace APPS\blog;

\Ko_Web_Route::VGet('index', function () {
	$uid = \Ko_App_Rest::VInvoke('user', 'GET', 'loginuid/');
	$blogid = \Ko_Web_Request::IGet('blogid');

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new MApi();
	$taginfos = $blogApi->aGetAllTaginfo($uid);

	$contentApi = \Ko_App_Rest::VInvoke('content', 'POST', 'object/');
	$contentApi = $contentApi['key'];
	$htmlrender = new \Ko_View_Render_HTML($contentApi);
	if ($blogid) {
		$bloginfo = $blogApi->aGet($uid, $blogid);
		if (empty($bloginfo) || in_array('回收站', $bloginfo['tags'])) {
			\Ko_Web_Response::VSetRedirect('user?uid='.$uid);
			\Ko_Web_Response::VSend();
			exit;
		}

		$htmlrender->oSetData(\KContent_Const::BLOG_TITLE, $blogid);
		$htmlrender->oSetData(\KContent_Const::BLOG_CONTENT, $blogid);
	} else {
		$bloginfo = array();

		$htmlrender->oSetData(\KContent_Const::DRAFT_CONTENT, $uid);
		$htmlrender->oSetData(\KContent_Const::DRAFT_TITLE, $uid);
	}

	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
	$render = $render['key'];
	$render->oSetTemplate('post.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('bloginfo', $bloginfo)
		->oSetData('blogcontent', $htmlrender)
		->oSetData('taginfos', $taginfos)
		->oSend();
});
