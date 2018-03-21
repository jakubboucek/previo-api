<?php

declare(strict_types=1);

namespace NAttreid\PrevioApi\Hooks;

use NAttreid\Form\Form;
use NAttreid\PrevioApi\PrevioClientException;
use NAttreid\PrevioApi\CredentialsNotSetException;
use NAttreid\PrevioApi\PrevioClient;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\InvalidArgumentException;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * Class PrevioHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class PrevioHook extends HookFactory
{
	/** @var IConfigurator */
	protected $configurator;

	public function init(): void
	{
		if (!$this->configurator->previo) {
			$this->configurator->previo = new PrevioConfig;
		}
	}

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();

		$form->addText('login', 'webManager.web.hooks.previo.login')
			->setDefaultValue($this->configurator->previo->login);
		$form->addText('password', 'webManager.web.hooks.previo.password')
			->setDefaultValue($this->configurator->previo->password);
		$form->addInteger('hotelId', 'webManager.web.hooks.previo.hotelId')
			->setDefaultValue($this->configurator->previo->hotelId)
			->setRequired(false);

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'previoFormSucceeded'];

		return $form;
	}

	public function previoFormSucceeded(Form $form, ArrayHash $values): void
	{
		$config = $this->configurator->previo;

		$config->login = $values->login ?: null;
		$config->password = $values->password ?: null;
		$config->hotelId = $values->hotelId ?: null;

		$this->configurator->previo = $config;

		$this->flashNotifier->success('default.dataSaved');

		$this->onDataChange();
	}
}