<?php

declare(strict_types=1);

namespace NAttreid\PrevioApi\DI;

use NAttreid\PrevioApi\Hooks\PrevioConfig;
use NAttreid\PrevioApi\PrevioClient;
use Nette\DI\CompilerExtension;

/**
 * Class AbstractPrevioApiExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
 class AbstractPrevioApiExtension extends CompilerExtension
{
	private $defaults = [
		'login' => null,
		'password' => null,
		'hotelId' => null,
		'debug' => false
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$previo = $this->prepareHook($config);

		$builder->addDefinition($this->prefix('client'))
			->setType(PrevioClient::class)
			->setArguments([$config['debug'], $previo]);
	}

	protected function prepareHook(array $config)
	{
		$previo = new PrevioConfig;
		$previo->login = $config['login'];
		$previo->password = $config['password'];
		$previo->hotelId = $config['hotelId'];
		return $previo;
	}
}