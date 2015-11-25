<?php

class KRest_Api extends Ko_Mode_Rest
{
	protected function _sGetClassname($sModule, $sResource)
	{
		$item = explode('/', $sModule);
		$classname = 'KRest_';
		foreach ($item as $v)
		{
			$classname .= ucfirst($v).'_';
		}
		$classname .= $sResource;
		return $classname;
	}

	protected function _aLoadConf($sModule, $sResource)
	{
		$classname = $this->_sGetClassname($sModule, $sResource);
		if (!class_exists($classname) || !isset($classname::$s_aConf))
		{
			throw new Exception('资源不存在', self::ERROR_RESOURCE_INVALID);
		}
		return $classname::$s_aConf;
	}

	public function run()
	{
		$uri = Ko_Web_Request::SGet('uri');
		$req_method = Ko_Web_Request::SRequestMethod(true);
		if ('POST' === $req_method)
		{
			$method = Ko_Web_Request::SPost('method');
			if ('PUT' === $method || 'DELETE' === $method)
			{
				$req_method = $method;
			}
		}
		$input = ('GET' === $req_method) ? $_GET : $_POST;
		unset($input['uri']);
		unset($input['method']);
		if (isset($input['jsondata']))
		{
			$input = json_decode($input['jsondata'], true);
		}

		$rest = new self;
		$data = $rest->aCall($req_method, $uri, $input);

		$render = new KRender_json();
		$render->oSetData($data)->oSend();
	}
}
