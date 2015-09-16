<?php

Ko_Web_Route::VGet('user', function () {
	static $num = 10;

	$uid = Ko_Web_Request::IGet('uid');
	$tag = Ko_Web_Request::SGet('tag');
	$page = max(1, Ko_Web_Request::IGet('page'));
	if (0 == strlen($tag)) {
		$tag = '全部';
	}

	$userinfo = Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new KBlog_Api();
	$taginfos = $blogApi->aGetAllTaginfo($uid);
	$bloglist = $blogApi->aGetBlogList($uid, $tag, ($page - 1) * $num, $num, $total);
	if (empty($bloglist)) {
		if (strlen($tag)) {
			if (1 == $page) {
				Ko_Web_Response::VSetRedirect('?uid='.$uid);
			} else {
				Ko_Web_Response::VSetRedirect('?uid='.$uid.'&tag='.urlencode($tag));
			}
			Ko_Web_Response::VSend();
			exit;
		} else if (1 != $page) {
			Ko_Web_Response::VSetRedirect('?uid='.$uid);
			Ko_Web_Response::VSend();
			exit;
		}
	}
	if ('回收站' === $tag) {
		$loginApi = new KUser_loginApi();
		$loginuid = $loginApi->iGetLoginUid();
		if ($loginuid != $uid) {
			Ko_Web_Response::VSetRedirect('?uid='.$uid);
			Ko_Web_Response::VSend();
			exit;
		}
	}
	$blogids = Ko_Tool_Utils::AObjs2ids($bloglist, 'blogid');

	$contentApi = new KContent_Api();
	$htmlrender = new Ko_View_Render_HTML($contentApi);
	$htmlrender->oSetData(KContent_Api::BLOG_TITLE, $blogids);
	$htmlrender->oSetData(KContent_Api::BLOG_CONTENT, array('ids' => $blogids, 'maxlength' => 1000));

	$page = array(
		'num' => $num,
		'no' => $page,
		'data_total' => $total,
	);
	$render = new KRender_www;
	$render->oSetTemplate('www/blog/user.html')
		->oSetData('tag', $tag)
		->oSetData('userinfo', $userinfo)
		->oSetData('taginfos', $taginfos)
		->oSetData('bloglist', $bloglist)
		->oSetData('bloghtml', $htmlrender)
		->oSetData('page', $page)
		->oSend();
});

Ko_Web_Route::VGet('post', function () {
	$loginApi = new KUser_loginApi();
	$uid = $loginApi->iGetLoginUid();
	$blogid = Ko_Web_Request::IGet('blogid');

	$userinfo = Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new KBlog_Api();
	$taginfos = $blogApi->aGetAllTaginfo($uid);

	$contentApi = new KContent_Api();
	$htmlrender = new Ko_View_Render_HTML($contentApi);
	if ($blogid) {
		$bloginfo = $blogApi->aGet($uid, $blogid);
		if (empty($bloginfo) || in_array('回收站', $bloginfo['tags'])) {
			Ko_Web_Response::VSetRedirect('user?uid='.$uid);
			Ko_Web_Response::VSend();
			exit;
		}

		$htmlrender->oSetData(KContent_Api::BLOG_TITLE, $blogid);
		$htmlrender->oSetData(KContent_Api::BLOG_CONTENT, $blogid);
	} else {
		$bloginfo = array();

		$htmlrender->oSetData(KContent_Api::DRAFT_CONTENT, $uid);
		$htmlrender->oSetData(KContent_Api::DRAFT_TITLE, $uid);
	}

	$render = new KRender_www;
	$render->oSetTemplate('www/blog/post.html')
		->oSetData('userinfo', $userinfo)
		->oSetData('bloginfo', $bloginfo)
		->oSetData('blogcontent', $htmlrender)
		->oSetData('taginfos', $taginfos)
		->oSend();
});

Ko_Web_Route::VGet('item', function () {
	$uid = Ko_Web_Request::IGet('uid');
	$blogid = Ko_Web_Request::IGet('blogid');
	$tag = Ko_Web_Request::SGet('tag');

	$userinfo = Ko_Tool_Adapter::VConv($uid, array('user_baseinfo', array('logo80')));

	$blogApi = new KBlog_Api();
	$taginfos = $blogApi->aGetAllTaginfo($uid);
	$bloginfo = $blogApi->aGet($uid, $blogid);
	if (empty($bloginfo) || in_array('回收站', $bloginfo['tags'])) {
		Ko_Web_Response::VSetRedirect('user?uid='.$uid);
		Ko_Web_Response::VSend();
		exit;
	}

	if (0 == strlen($tag)) {
		$tag = $blogApi->sGetPriorTag($bloginfo['tags']);
	}
	$prevnextInfo = $blogApi->aGetPrevNextTitle($uid, $blogid, $tag);

	$contentApi = new KContent_Api();
	$htmlrender = new Ko_View_Render_HTML($contentApi);
	$htmlrender->oSetData(KContent_Api::BLOG_TITLE, $blogid);
	$htmlrender->oSetData(KContent_Api::BLOG_CONTENT, $blogid);

	$render = new KRender_www;
	$render->oSetTemplate('www/blog/item.html')
		->oSetData('tag', $tag)
		->oSetData('prevnext', $prevnextInfo)
		->oSetData('userinfo', $userinfo)
		->oSetData('bloginfo', $bloginfo)
		->oSetData('blogcontent', $htmlrender)
		->oSetData('taginfos', $taginfos)
		->oSend();
});
