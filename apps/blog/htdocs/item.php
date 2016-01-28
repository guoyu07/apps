<?php

namespace APPS\blog;

\Ko_Web_Route::VGet('index', function () {
	$uid = \Ko_Web_Request::IGet('uid');
	$blogid = \Ko_Web_Request::IGet('blogid');
	$tag = \Ko_Web_Request::SGet('tag');

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new MApi();
	$taginfos = $blogApi->aGetAllTaginfo($uid);
	$bloginfo = $blogApi->aGet($uid, $blogid);
	if (empty($bloginfo) || in_array('回收站', $bloginfo['tags'])) {
		\Ko_Web_Response::VSetRedirect('user?uid='.$uid);
		\Ko_Web_Response::VSend();
		exit;
	}

	if (0 == strlen($tag)) {
		$tag = $blogApi->sGetPriorTag($bloginfo['tags']);
	}
	$prevnextInfo = $blogApi->aGetPrevNextTitle($uid, $blogid, $tag);

	$contentApi = \Ko_App_Rest::VInvoke('content', 'POST', 'object/');
	$contentApi = $contentApi['key'];
	$htmlrender = new \Ko_View_Render_HTML($contentApi);
	$htmlrender->oSetData(\KContent_Const::BLOG_TITLE, $blogid);
	$htmlrender->oSetData(\KContent_Const::BLOG_CONTENT, $blogid);

	$render = \Ko_App_Rest::VInvoke('render', 'POST', 'object/');
	$render = $render['key'];
	$render->oSetTemplate('item.html')
		->oSetData('tag', $tag)
		->oSetData('prevnext', $prevnextInfo)
		->oSetData('userinfo', $userinfo)
		->oSetData('bloginfo', $bloginfo)
		->oSetData('blogcontent', $htmlrender)
		->oSetData('taginfos', $taginfos)
		->oSend();
});
