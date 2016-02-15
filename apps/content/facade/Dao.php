<?php

namespace APPS\content;

class MFacade_Dao extends \Ko_Dao_Factory
{
	protected $_aDaoConf = array(
		'changelog' => array(
			'type' => 'db_single',
			'kind' => 'common_changelog',
			'key' => 'id',
		),
		'content' => array(
			'type' => 'db_single',
			'kind' => 'common_content',
			'key' => array('id', 'aid'),
		),
	);
}
