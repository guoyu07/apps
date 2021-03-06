<?php

namespace APPS\blog;

\Ko_Web_Route::VGet('index', function () {
	static $num = 10;

	$uid = \Ko_Web_Request::IGet('uid');
	$tag = \Ko_Web_Request::SGet('tag');
	$page = max(1, \Ko_Web_Request::IGet('page'));
	if (0 == strlen($tag)) {
		$tag = '全部';
	}

	$userinfo = \Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new MApi();
	$taginfos = $blogApi->aGetAllTaginfo($uid);
	$bloglist = $blogApi->aGetBlogList($uid, $tag, ($page - 1) * $num, $num, $total);
	if (empty($bloglist)) {
		if ('全部' !== $tag) {
			if (1 == $page) {
				\Ko_Web_Response::VSetRedirect('?uid='.$uid);
			} else {
				\Ko_Web_Response::VSetRedirect('?uid='.$uid.'&tag='.urlencode($tag));
			}
			\Ko_Web_Response::VSend();
			exit;
		} else if (1 != $page) {
			\Ko_Web_Response::VSetRedirect('?uid='.$uid);
			\Ko_Web_Response::VSend();
			exit;
		}
	}
	if ('回收站' === $tag) {
		$loginuid = \APPS\user\MFacade_Api::getLoginUid();
		if ($loginuid != $uid) {
			\Ko_Web_Response::VSetRedirect('?uid='.$uid);
			\Ko_Web_Response::VSend();
			exit;
		}
	}
	$blogids = \Ko_Tool_Utils::AObjs2ids($bloglist, 'blogid');

	$contentApi = new \APPS\content\MFacade_Api();
	$htmlrender = new \Ko_View_Render_HTML($contentApi);
	$htmlrender->oSetData(\APPS\content\MFacade_Const::BLOG_TITLE, $blogids);
	$htmlrender->oSetData(\APPS\content\MFacade_Const::BLOG_CONTENT, array('ids' => $blogids, 'maxlength' => 1000));

	$page = array(
		'num' => $num,
		'no' => $page,
		'data_total' => $total,
	);
	$render = new \APPS\render\MFacade_default();
	$render->oSetTemplate('user.html')
		->oSetData('tag', $tag)
		->oSetData('userinfo', $userinfo)
		->oSetData('taginfos', $taginfos)
		->oSetData('bloglist', $bloglist)
		->oSetData('bloghtml', $htmlrender)
		->oSetData('page', $page)
		->oSend();
});
