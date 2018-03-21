<?php

declare(strict_types=1);

namespace NAttreid\PrevioApi\Hooks;

use Nette\SmartObject;

/**
 * Class PrevioConfig
 *
 * @property string $login
 * @property string $password
 * @property int $hotelId
 *
 * @author Attreid <attreid@gmail.com>
 */
class PrevioConfig
{
	use SmartObject;

	/** @var string */
	private $login;

	/** @var string */
	private $password;

	/** @var int */
	private $hotelId;

	protected function getLogin(): ?string
	{
		return $this->login;
	}

	protected function setLogin(?string $login)
	{
		$this->login = $login;
	}

	protected function getPassword(): ?string
	{
		return $this->password;
	}

	protected function setPassword(?string $password)
	{
		$this->password = $password;
	}

	protected function getHotelId(): ?int
	{
		return $this->hotelId;
	}

	protected function setHotelId(?int $hotelId)
	{
		$this->hotelId = $hotelId;
	}
}