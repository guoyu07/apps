<?php

namespace APPS\sysmsg;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'content' => array(
			'type' => 'db_single',
			'kind' => 'sysmsg_content',
			'key' => 'msgid',
		),
		'user' => array(
			'type' => 'db_single',
			'kind' => 'sysmsg_user',
			'key' => array('uid', 'msgid'),
		),
		'merge' => array(
			'type' => 'db_single',
			'kind' => 'sysmsg_merge',
			'key' => array('uid', 'msgtype', 'mergeid'),
		),
	);
}
