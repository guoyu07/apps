<?php

namespace APPS\blog;

\Ko_Web_Route::VGet('index', function () {
	$uid = \APPS\user\MFacade_Api::getLoginUid();
	$blogid = \Ko_Web_Request::IGet('blogid');

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new MApi();
	$taginfos = $blogApi->aGetAllTaginfo($uid);

	$contentApi = new \APPS\content\MFacade_Api();
	$htmlrender = new \Ko_View_Render_HTML($contentApi);
	if ($blogid) {
		$bloginfo = $blogApi->aGet($uid, $blogid);
		if (empty($bloginfo) || in_array('回收站', $bloginfo['tags'])) {
			\Ko_Web_Response::VSetRedirect('user?uid='.$uid);
			\Ko_Web_Response::VSend();
			exit;
		}

		$htmlrender->oSetData(\APPS\content\MFacade_Const::BLOG_TITLE, $blogid);
		$htmlrender->oSetData(\APPS\content\MFacade_Const::BLOG_CONTENT, $blogid);
	} else {
		$bloginfo = array();

		$htmlrender->oSetData(\APPS\content\MFacade_Const::DRAFT_CONTENT, $uid);
		$htmlrender->oSetData(\APPS\content\MFacade_Const::DRAFT_TITLE, $uid);
	}

	$token = \APPS\storage\MFacade_Api::getUploadImageToken(array('type' => 'blog', 'decorate' => 'imageView2/2/w/600/h/600'));

	$render = new \APPS\render\MFacade_default();
	$render->oSetTemplate('post.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('bloginfo', $bloginfo)
		->oSetData('blogcontent', $htmlrender)
		->oSetData('taginfos', $taginfos)
		->oSetData('token', $token)
		->oSend();
});
