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

		$previo = $this->prepareConfig($config);

		$builder->addDefinition($this->prefix('client'))
			->setType(PrevioClient::class)
			->setArguments([$config['debug'], $previo]);
	}

	protected function prepareConfig(array $config)
	{
		$builder = $this->getContainerBuilder();
		return $builder->addDefinition($this->prefix('config'))
			->setFactory(PrevioConfig::class)
			->addSetup('$login', [$config['login']])
			->addSetup('$password', [$config['password']])
			->addSetup('$hotelId', [$config['hotelId']]);
	}
}