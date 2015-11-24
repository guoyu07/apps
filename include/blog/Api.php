<?php

class KBlog_Api extends Ko_Busi_Api
{
	public function aGetPrevNextTitle($uid, $blogid, $tag)
	{
		$taglistkey = compact('uid', 'blogid', 'tag');
		$info = $this->taglistDao->aGet($taglistkey);
		$blogids = array();
		if ($info['prev']) {
			$blogids[] = $info['prev'];
		}
		if ($info['next']) {
			$blogids[] = $info['next'];
		}
		$ret = array();
		if (!empty($blogids)) {
			$aTitle = Ko_Apps_Rest::VInvoke('content', 'GET', 'item/', array(
				'filter' => array(
					'aid' => KContent_Const::BLOG_TITLE,
					'ids' => $blogids,
				),
			));
			$aTitle = $aTitle['list'];
			if ($info['prev']) {
				$ret['prev'] = array('blogid' => $info['prev'], 'title' => $aTitle[$info['prev']]);
			}
			if ($info['next']) {
				$ret['next'] = array('blogid' => $info['next'], 'title' => $aTitle[$info['next']]);
			}
		}
		return $ret;
	}

	public function sGetPriorTag($tags)
	{
		$tagcount = count($tags);
		if (1 == $tagcount) {
			return $tag = $tags[0];
		} else if (1 < $tagcount) {
			$tags = array_values(array_diff($tags, array('未分类')));
			$tagcount = count($tags);
			if (1 == $tagcount) {
				return $tag = $tags[0];
			} else if (1 < $tagcount) {
				$tags = array_values(array_diff($tags, array('全部')));
				$tagcount = count($tags);
				if (1 == $tagcount) {
					return $tag = $tags[0];
				}
			}
		}
		return '全部';
	}

	public function aGetBlogList($uid, $tag, $start, $num, &$total)
	{
		$option = new Ko_Tool_SQL();
		$offset = ($start > 0) ? $start - 1 : $start;
		$addnum = ($start > 0) ? 2 : 1;
		$limit = $num + $addnum;
		$option->oOffset($offset)->oLimit($limit)->oOrderBy('blogid desc')->oCalcFoundRows(true);
		$list = $this->taglistDao->aGetList($option->oWhere('uid = ? and tag = ?', $uid, $tag));
		if ($count = count($list)) {
			if ($start > 0) {
				$prev = array_shift($list);
				$count--;
				$prev = $prev['blogid'];
			} else {
				$prev = 0;
			}
			if ($count > $num) {
				$next = array_pop($list);
				$count--;
				$next = $next['blogid'];
			} else {
				$next = 0;
			}
		}
		$infos = $this->blogDao->aGetDetails($list);
		foreach ($list as $k => &$v) {
			$update = array();
			if ($k != 0) {
				if ($list[$k - 1]['blogid'] != $v['prev']) {
					$update['prev'] = $v['prev'] = $list[$k - 1]['blogid'];
				}
			} else {
				if ($prev != $v['prev']) {
					$update['prev'] = $v['prev'] = $prev;
				}
			}
			if ($k != $count - 1) {
				if ($list[$k + 1]['blogid'] != $v['next']) {
					$update['next'] = $v['next'] = $list[$k + 1]['blogid'];
				}
			} else {
				if ($next != $v['next']) {
					$update['next'] = $v['next'] = $next;
				}
			}
			if (!empty($update)) {
				$this->taglistDao->iUpdate($v, $update);
			}
			$v = $infos[$v['blogid']];
			if (strlen($v['cover'])) {
				$v['cover'] = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$v['cover'], array('data_decorate' => 'imageView2/1/w/300/h/200'));
			}
		}
		unset($v);
		$total = $option->iGetFoundRows();
		$taginfokey = compact('uid', 'tag');
		$taginfo = $this->taginfoDao->aGet($taginfokey);
		if ($taginfo['bcount'] != $total) {
			$data = array(
				'uid' => $uid,
				'tag' => $tag,
				'bcount' => $total,
				'mtime' => date('Y-m-d H:i:s'),
			);
			$update = array(
				'bcount' => $total,
			);
			$this->taginfoDao->aInsert($data, $update);
		}
		return $list;
	}

	public function aGetAllTaginfo($uid)
	{
		$option = new Ko_Tool_SQL();
		$list = $this->taginfoDao->aGetList($option->oWhere('uid = ?', $uid)->oOrderBy('mtime desc'));
		$ret = array();
		foreach ($list as $v) {
			if (0 == $v['bcount']) {
				$taginfokey = array(
					'uid' => $uid,
					'tag' => $v['tag'],
				);
				$this->taginfoDao->iDelete($taginfokey);
			} else {
				$ret[] = $v;
			}
		}
		return $ret;
	}

	public function aGetBlogInfos($list)
	{
		$blogids = Ko_Tool_Utils::AObjs2ids($list, 'blogid');
		$infos = $this->blogDao->aGetDetails($list);
		$aText = Ko_Apps_Rest::VInvoke('content', 'GET', 'items/', array(
			'filter' => array(
				KContent_Const::BLOG_TITLE => $blogids,
				KContent_Const::BLOG_CONTENT => array('ids' => $blogids, 'maxlength' => 1000, 'ext' => '...'),
			),
		));
		$aText = $aText['list'];
		foreach ($infos as &$v) {
			if ('回收站' === $v['tags']) {
				$v = array();
			}
			if (!empty($v)) {
				$v['title'] = $aText[KContent_Const::BLOG_TITLE][$v['blogid']];
				$v['content'] = $aText[KContent_Const::BLOG_CONTENT][$v['blogid']];
				if (strlen($v['cover'])) {
					$v['cover'] = Ko_Apps_Rest::VInvoke('storage', 'GET', 'item/'.$v['cover'], array('data_decorate' => 'imageView2/1/w/300/h/200'));
				}
			}
		}
		unset($v);
		return $infos;
	}

	public function aGet($uid, $blogid)
	{
		$blogkey = compact('uid', 'blogid');
		$info = $this->blogDao->aGet($blogkey);
		if (!empty($info)) {
			$info['tags'] = $this->_aGetTags($info['tags']);
		}
		return $info;
	}

	public function iInsert($uid, $title, $content, $tags)
	{
		if (!$uid) {
			return 0;
		}
		$addtags = $this->_aGetTags($tags);
		$data = array(
			'uid' => $uid,
			'ctime' => date('Y-m-d H:i:s'),
			'cover' => $this->_sGetCover($content),
			'tags' => implode(' ', $addtags),
		);
		$data['mtime'] = $data['ctime'];
		$blogid = $this->blogDao->iInsert($data);
		if ($blogid) {
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_TITLE.'_'.$blogid, array(
				'update' => $title,
			));
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_CONTENT.'_'.$blogid, array(
				'update' => $content,
			));

			$this->_vAddTags($uid, $blogid, $addtags);
		}
		return $blogid;
	}

	public function iUpdate($uid, $blogid, $title, $content, $tags)
	{
		$blogkey = compact('uid', 'blogid');
		$info = $this->blogDao->aGet($blogkey);
		if (empty($info)) {
			return 0;
		}
		if ('回收站' === $info['tags']) {
			return 0;
		}

		$oldtagarr = $this->_aGetTags($info['tags']);
		$newtagarr = $this->_aGetTags($tags);
		$addtags = array_diff($newtagarr, $oldtagarr);
		$this->_vAddTags($uid, $blogid, $addtags);
		$subtags = array_diff($oldtagarr, $newtagarr);
		$this->_vSubTags($uid, $blogid, $subtags);

		Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_TITLE.'_'.$blogid, array(
			'update' => $title,
		));
		Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_CONTENT.'_'.$blogid, array(
			'update' => $content,
		));

		$update = array(
			'mtime' => date('Y-m-d H:i:s'),
			'tags' => implode(' ', $newtagarr),
		);
		if (0 == strlen($info['cover'])) {
			$update['cover'] = $this->_sGetCover($content);
		}
		return $this->blogDao->iUpdate($blogkey, $update);
	}

	public function iDelete($uid, $blogid)
	{
		$blogkey = compact('uid', 'blogid');
		$info = $this->blogDao->aGet($blogkey);
		if (empty($info)) {
			return 0;
		}
		if ('回收站' === $info['tags']) {
			$this->_vSubTags($uid, $blogid, array('回收站'));
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_TITLE.'_'.$blogid, array(
				'update' => '',
			));
			Ko_Apps_Rest::VInvoke('content', 'PUT', 'item/'.KContent_Const::BLOG_CONTENT.'_'.$blogid, array(
				'update' => '',
			));
			return $this->blogDao->iDelete($blogkey);
		} else {
			$subtags = $this->_aGetTags($info['tags']);
			$this->_vSubTags($uid, $blogid, $subtags);
			$this->_vAddTags($uid, $blogid, array('回收站'));
			$update = array(
				'mtime' => date('Y-m-d H:i:s'),
				'tags' => '回收站',
			);
			return $this->blogDao->iUpdate($blogkey, $update);
		}
	}

	public function iReset($uid, $blogid)
	{
		$blogkey = compact('uid', 'blogid');
		$info = $this->blogDao->aGet($blogkey);
		if (empty($info)) {
			return 0;
		}
		if ('回收站' !== $info['tags']) {
			return 0;
		}
		$this->_vSubTags($uid, $blogid, array('回收站'));
		$this->_vAddTags($uid, $blogid, array('全部', '未分类'));
		$update = array(
			'mtime' => date('Y-m-d H:i:s'),
			'tags' => '全部 未分类',
		);
		return $this->blogDao->iUpdate($blogkey, $update);
	}

	private function _vAddTags($uid, $blogid, $tags)
	{
		foreach ($tags as $tag) {
			$taglistkey = compact('uid', 'tag', 'blogid');
			try {
				$this->taglistDao->aInsert($taglistkey);

				$mtime = date('Y-m-d H:i:s');
				$taginfokey = compact('uid', 'tag', 'mtime');
				$taginfokey['bcount'] = 1;
				$this->taginfoDao->aInsert($taginfokey, array('mtime' => $mtime), array('bcount' => 1));
			} catch (Exception $e) {
			}
		}
	}

	private function _vSubTags($uid, $blogid, $tags)
	{
		foreach ($tags as $tag) {
			$taglistkey = compact('uid', 'tag', 'blogid');
			if ($this->taglistDao->iDelete($taglistkey)) {
				$taginfokey = compact('uid', 'tag');
				$option = new Ko_Tool_SQL();
				$this->taginfoDao->iUpdate($taginfokey, array(), array('bcount' => -1), $option->oWhere('bcount > ?', 0));
			}
		}
	}

	private function _aGetTags($tags)
	{
		if ('回收站' === $tags) {
			return array('回收站');
		}
		$tags = explode(' ', $tags);
		$tags[] = '全部';
		$tags = array_values(array_diff(array_unique($tags), array('', '未分类', '回收站')));
		if (1 === count($tags)) {
			$tags[] = '未分类';
		}
		foreach ($tags as &$tag) {
			$tag = Ko_Tool_Str::SSubStr_UTF8($tag, 60);
		}
		unset($tag);
		return $tags;
	}

	private function _sGetCover($content)
	{
		$data = Ko_Apps_Rest::VInvoke('storage', 'POST', 'cover/', array('update' => $content));
		return $data['key'];
	}
}
