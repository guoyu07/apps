<?php

namespace APPS\user;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'username' => array(
			'type' => 'db_single',
			'kind' => 'user_username',
			'key' => array('username', 'src'),
		),
		'bindlog' => array(
			'type' => 'db_single',
			'kind' => 'user_bindlog',
			'key' => 'uid',
		),
		'hashpass' => array(
			'type' => 'db_single',
			'kind' => 'user_hashpass',
			'key' => 'uid',
		),
		'varsalt' => array(
			'type' => 'db_single',
			'kind' => 'user_varsalt',
			'key' => 'uid',
		),
		'persistent' => array(
			'type' => 'db_single',
			'kind' => 'user_cookie',
			'key' => array('uid', 'series'),
		),
		'changelog' => array(
			'type' => 'db_single',
			'kind' => 'user_changelog',
			'key' => 'id',
		),
		'baseinfo' => array(
			'type' => 'db_single',
			'kind' => 'user_baseinfo',
			'key' => 'uid',
		),
		'uuid' => array(
			'type' => 'db_single',
			'kind' => 'user_uuid',
			'key' => 'uuid',
		),
	);
}
