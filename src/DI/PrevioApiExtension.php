<?php

declare(strict_types=1);

namespace NAttreid\PrevioApi\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\PrevioApi\Hooks\PrevioConfig;
use NAttreid\PrevioApi\Hooks\PrevioHook;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\Statement;

if (trait_exists('NAttreid\Cms\DI\ExtensionTranslatorTrait')) {
	class PrevioApiExtension extends AbstractPrevioApiExtension
	{
		use ExtensionTranslatorTrait;

		protected function prepareConfig(array $config)
		{
			$builder = $this->getContainerBuilder();
			$hook = $builder->getByType(HookService::class);
			if ($hook) {
				$builder->addDefinition($this->prefix('previoHook'))
					->setType(PrevioHook::class);

				$this->setTranslation(__DIR__ . '/../lang/', [
					'webManager'
				]);

				return new Statement('?->previo \?: new ' . PrevioConfig::class, ['@' . Configurator::class]);
			} else {
				return parent::prepareConfig($config);
			}
		}
	}
} else {
	class PrevioApiExtension extends AbstractPrevioApiExtension
	{
	}
}