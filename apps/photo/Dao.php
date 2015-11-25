<?php

namespace APPS\photo;

class MDao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'albumtag' => array(
			'type' => 'db_single',
			'kind' => 'photo_albumtag',
			'key' => array('uid', 'albumtag'),
		),
		'album' => array(
			'type' => 'db_single',
			'kind' => 'photo_album',
			'key' => array('albumid', 'uid'),
		),
		'photo' => array(
			'type' => 'db_single',
			'kind' => 'photo_list',
			'key' => array('photoid', 'uid'),
		),
		'albumdigest' => array(
			'type' => 'db_single',
			'kind' => 'photo_albumdigest',
			'key' => array('albumid'),
		),
	);
}
