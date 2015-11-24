<?php

namespace APPS\blog;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'blog' => array(
			'type' => 'db_single',
			'kind' => 'blog_info',
			'key' => array('blogid', 'uid'),
		),
		'taginfo' => array(
			'type' => 'db_single',
			'kind' => 'blog_tag_info',
			'key' => array('uid', 'tag'),
		),
		'taglist' => array(
			'type' => 'db_single',
			'kind' => 'blog_tag_list',
			'key' => array('uid', 'tag', 'blogid'),
		),
	);
}
